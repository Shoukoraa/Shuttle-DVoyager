<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\SocialAccount;
use App\Models\Role;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    private const OTP_TTL_MINUTES = 5;
    private const OTP_RESEND_DELAY_SECONDS = 60;
    private const PASSWORD_RESET_TTL_MINUTES = 10;

    public function redirectToGoogle(Request $request)
    {
        $redirectUrl = $request->query("redirect");
        $state = null;

        if ($redirectUrl) {
            $state = base64_encode(json_encode([
                "redirect" => $redirectUrl,
            ]));
        }

        $google = Socialite::driver("google")
            ->stateless()
            ->with(["prompt" => "select_account"]);

        if ($state) {
            $google = $google->with(["state" => $state]);
        }

        return $google->redirect();
    }

    public function loginAdmin(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);

        $credentials = $request->only("email", "password");

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                "email" => "Email atau password salah"
            ]);
        }

        $user = Auth::user();

        // PROTEKSI: hanya admin boleh login di sini
        if ($user->role->name !== "admin") {
            Auth::logout();

            return back()->withErrors([
                "email" => "Akses hanya untuk admin"
            ]);
        }

        return redirect("/admin/dashboard");
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver("google")->stateless()->user();

            if (!$googleUser->email) {
                return response()->json(["message" => "Google account has no email"], 400);
            }

            $user = DB::transaction(function () use ($googleUser) {
                $account = SocialAccount::where("provider", "google")
                    ->where("provider_id", $googleUser->id)
                    ->first();

                if ($account) {
                    $linkedUser = User::withTrashed()->find($account->user_id);

                    if ($linkedUser) {
                        if ($linkedUser->trashed()) {
                            $linkedUser->restore();
                        }

                        if (!$linkedUser->role || $linkedUser->role->name !== "customer") {
                            abort(403, "Login Google hanya untuk customer");
                        }

                        $this->ensureCustomerRecord($linkedUser);

                        return $linkedUser;
                    }

                    $account->delete();
                }

                $user = User::withTrashed()->where("email", $googleUser->email)->first();

                if ($user) {
                    if ($user->trashed()) {
                        $user->restore();
                    }

                    if (!$user->role || $user->role->name !== "customer") {
                        abort(403, "Login Google hanya untuk customer");
                    }
                } else {
                    $role = Role::where("name", "customer")->first();
                    if (!$role) throw new \Exception("Role customer tidak ditemukan");

                    $user = User::create([
                        "name" => $googleUser->name ?? "User",
                        "email" => $googleUser->email,
                        "password" => null,
                        "role_id" => $role->id
                    ]);
                }

                SocialAccount::updateOrCreate(
                    ["provider" => "google", "provider_id" => $googleUser->id],
                    ["user_id" => $user->id, "provider_email" => $googleUser->email]
                );

                $this->ensureCustomerRecord($user);

                return $user;
            });

            if (!$user->role || $user->role->name !== "customer") {
                return response()->json(["message" => "Unauthorized"], 403);
            }

            $token = $user->createToken("auth_token")->plainTextToken;
            
            $stateParam = $request->query("state");
            $frontendRedirect = "http://localhost:3000/auth/callback";

            if ($stateParam) {
                $stateData = json_decode(base64_decode($stateParam), true);
                if (isset($stateData["redirect"])) {
                    $frontendRedirect = $stateData["redirect"];
                }
            }

            $separator = str_contains($frontendRedirect, "?") ? "&" : "?";
            return redirect()->away(
                $frontendRedirect . $separator . "oauth=google&token=" . urlencode($token) . "&role=customer"
            );

        } catch (\Throwable $e) {
            Log::warning('Google OAuth failed: ' . $e->getMessage());

            $frontendRedirect = $this->frontendRedirectFromState($request->query("state"));
            $separator = str_contains($frontendRedirect, "?") ? "&" : "?";

            return redirect()->away(
                $frontendRedirect . $separator . "oauth_error=" . urlencode($this->googleOAuthErrorMessage($e))
            );
        }
    }

    public function getUserProfile(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(["message" => "Unauthenticated"], 401);
            }

            return response()->json([
                "id" => (int)$user->id,
                "name" => (string)$user->name,
                "email" => (string)$user->email,
                "phone" => (string)($user->phone ?? ''),
                "role_id" => (int)$user->role_id,
                "has_password" => !empty($user->password),
            ]);
        } catch (\Throwable $e) {
            \Log::error('getUserProfile: ' . $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(["message" => "Server error"], 500);
        }
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => [
                "required",
                "email",
                Rule::unique("users", "email")->whereNull("deleted_at"),
            ],
            "phone" => "nullable|string|max:20",
            "password" => "required|string|min:8|confirmed",
        ]);

        try {
            $user = DB::transaction(function () use ($validated) {
                $role = Role::where("name", "customer")->first();
                if (!$role) throw new \Exception("Role customer tidak ditemukan");

                $user = User::withTrashed()->where("email", $validated["email"])->first();

                if ($user) {
                    if (!$user->trashed()) {
                        throw new \Exception("Email sudah digunakan akun lain");
                    }

                    if ((int) $user->role_id !== (int) $role->id) {
                        throw new \Exception("Email pernah dipakai akun lain yang bukan customer");
                    }

                    $user->restore();
                    $user->update([
                        "name" => $validated["name"],
                        "email" => $validated["email"],
                        "phone" => $validated["phone"] ?? null,
                        "password" => bcrypt($validated["password"]),
                        "role_id" => $role->id,
                    ]);
                } else {
                    $user = User::create([
                        "name" => $validated["name"],
                        "email" => $validated["email"],
                        "phone" => $validated["phone"] ?? null,
                        "password" => bcrypt($validated["password"]),
                        "role_id" => $role->id
                    ]);
                }

                // Create corresponding Customer record so admin can see this user
                $customer = Customer::withTrashed()->where("user_id", $user->id)->first();
                if ($customer) {
                    if (method_exists($customer, 'trashed') && $customer->trashed()) {
                        $customer->restore();
                    }
                } else {
                    Customer::create(["user_id" => $user->id]);
                }

                return $user;
            });

            // Do not return token yet. Send OTP to the registered email
            $this->sendOtpToEmail($user->email, $user->name);

            $user->loadMissing("role");
            $userData = $user->toArray();
            $userData["role"] = $user->role ? $user->role->name : "customer";

            return response()->json([
                "message" => "Register berhasil. Kode OTP telah dikirim ke email.",
                "user" => $userData,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(["message" => "Register gagal", "error" => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        $user = User::where("email", $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["message" => "Email atau password salah"], 401);
        }

        $user->loadMissing("role");
        $role = $user->role ? $user->role->name : "customer";

        // PROTEKSI: Admin tidak boleh login ke mobile app
        if ($role === "admin") {
            return response()->json([
                "message" => "Akses hanya untuk customer dan driver. Gunakan admin dashboard untuk login."
            ], 403);
        }

        // Pastikan hanya customer atau driver yang bisa login
        if (!in_array($role, ["customer", "driver"])) {
            return response()->json([
                "message" => "Role tidak dikenal atau tidak diizinkan"
            ], 403);
        }

        $userData = $user->toArray();
        $userData["role"] = $role;

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "message" => "Login berhasil",
            "token" => $token,
            "user" => $userData
        ]);
    }

    public function requestOtp(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
        ]);

        $user = User::where("email", $validated["email"])->first();

        if (!$user) {
            return response()->json([
                "message" => "Email belum terdaftar"
            ], 404);
        }

        $retryAfterSeconds = $this->resendRetryAfterSeconds($user->email);
        if ($retryAfterSeconds > 0) {
            return response()->json([
                "message" => "Tunggu {$retryAfterSeconds} detik sebelum kirim ulang OTP.",
                "retry_after_seconds" => $retryAfterSeconds,
            ], 429);
        }

        $this->sendOtpToEmail($user->email, $user->name);

        return response()->json([
            "message" => "Kode OTP sudah dikirim ke email",
            "expires_in_minutes" => self::OTP_TTL_MINUTES,
            "resend_delay_seconds" => self::OTP_RESEND_DELAY_SECONDS,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
            "otp" => "required|digits:6",
        ]);

        $cacheKey = $this->otpCacheKey($validated["email"]);
        $hashedOtp = Cache::get($cacheKey);

        if (!$hashedOtp || !Hash::check($validated["otp"], $hashedOtp)) {
            return response()->json([
                "message" => "OTP salah atau sudah kedaluwarsa"
            ], 422);
        }

        Cache::forget($cacheKey);

        $user = User::where("email", $validated["email"])->first();

        if (!$user) {
            return response()->json([
                "message" => "Email belum terdaftar"
            ], 404);
        }

        $user->loadMissing("role");
        $role = $user->role ? $user->role->name : "customer";

        // PROTEKSI: Admin tidak boleh login ke mobile app
        if ($role === "admin") {
            return response()->json([
                "message" => "Akses hanya untuk customer dan driver. Gunakan admin dashboard untuk login."
            ], 403);
        }

        // Pastikan hanya customer atau driver yang bisa login
        if (!in_array($role, ["customer", "driver"])) {
            return response()->json([
                "message" => "Role tidak dikenal atau tidak diizinkan"
            ], 403);
        }

        $userData = $user->toArray();
        $userData["role"] = $role;

        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            "message" => "OTP valid",
            "token" => $token,
            "user" => $userData,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
        ]);

        $user = User::where("email", $validated["email"])->first();

        if (!$user) {
            return response()->json([
                "message" => "Email belum terdaftar"
            ], 404);
        }

        $retryAfterSeconds = $this->passwordResetRetryAfterSeconds($user->email);
        if ($retryAfterSeconds > 0) {
            return response()->json([
                "message" => "Tunggu {$retryAfterSeconds} detik sebelum kirim ulang kode reset.",
                "retry_after_seconds" => $retryAfterSeconds,
            ], 429);
        }

        $this->sendPasswordResetOtpToEmail($user->email, $user->name);

        return response()->json([
            "message" => "Kode reset password sudah dikirim ke email.",
            "expires_in_minutes" => self::PASSWORD_RESET_TTL_MINUTES,
            "resend_delay_seconds" => self::OTP_RESEND_DELAY_SECONDS,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            "email" => "required|email",
            "otp" => "required|digits:6",
            "password" => "required|string|min:8|confirmed",
        ]);

        $cacheKey = $this->passwordResetCacheKey($validated["email"]);
        $hashedOtp = Cache::get($cacheKey);

        if (!$hashedOtp || !Hash::check($validated["otp"], $hashedOtp)) {
            return response()->json([
                "message" => "Kode reset salah atau sudah kedaluwarsa"
            ], 422);
        }

        $user = User::where("email", $validated["email"])->first();

        if (!$user) {
            return response()->json([
                "message" => "Email belum terdaftar"
            ], 404);
        }

        $user->update([
            "password" => Hash::make($validated["password"]),
        ]);

        Cache::forget($cacheKey);
        Cache::forget($this->passwordResetResendKey($validated["email"]));

        return response()->json([
            "message" => "Password berhasil direset. Silakan login.",
        ]);
    }

    private function otpCacheKey(string $email): string
    {
        return 'otp:' . Str::lower($email);
    }

    private function otpResendKey(string $email): string
    {
        return 'otp_resend:' . Str::lower($email);
    }

    private function passwordResetCacheKey(string $email): string
    {
        return 'password_reset:' . Str::lower($email);
    }

    private function passwordResetResendKey(string $email): string
    {
        return 'password_reset_resend:' . Str::lower($email);
    }

    private function resendRetryAfterSeconds(string $email): int
    {
        $lockedUntil = Cache::get($this->otpResendKey($email));
        if (!$lockedUntil) {
            return 0;
        }

        $remaining = (int) $lockedUntil - now()->timestamp;
        return max(0, $remaining);
    }

    private function passwordResetRetryAfterSeconds(string $email): int
    {
        $lockedUntil = Cache::get($this->passwordResetResendKey($email));
        if (!$lockedUntil) {
            return 0;
        }

        $remaining = (int) $lockedUntil - now()->timestamp;
        return max(0, $remaining);
    }

    private function sendOtpToEmail(string $email, ?string $name = null): void
    {
        $otp = (string) random_int(100000, 999999);
        Cache::put($this->otpCacheKey($email), Hash::make($otp), now()->addMinutes(self::OTP_TTL_MINUTES));
        Cache::put(
            $this->otpResendKey($email),
            now()->timestamp + self::OTP_RESEND_DELAY_SECONDS,
            now()->addSeconds(self::OTP_RESEND_DELAY_SECONDS)
        );

        $this->sendOtpWithPhpMailer($email, $otp, $name);
    }

    private function sendPasswordResetOtpToEmail(string $email, ?string $name = null): void
    {
        $otp = (string) random_int(100000, 999999);
        Cache::put($this->passwordResetCacheKey($email), Hash::make($otp), now()->addMinutes(self::PASSWORD_RESET_TTL_MINUTES));
        Cache::put(
            $this->passwordResetResendKey($email),
            now()->timestamp + self::OTP_RESEND_DELAY_SECONDS,
            now()->addSeconds(self::OTP_RESEND_DELAY_SECONDS)
        );

        $this->sendOtpWithPhpMailer($email, $otp, $name, 'Kode Reset Password D-Voyager', self::PASSWORD_RESET_TTL_MINUTES);
    }

    private function sendOtpWithPhpMailer(
        string $email,
        string $otp,
        ?string $name = null,
        string $subject = 'Kode OTP D-Voyager',
        int $expiresInMinutes = self::OTP_TTL_MINUTES
    ): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = (string) config('mail.mailers.smtp.host');
            $mail->Port = (int) config('mail.mailers.smtp.port');
            $mail->SMTPAuth = (bool) config('mail.mailers.smtp.username');
            $mail->Username = (string) config('mail.mailers.smtp.username');
            $mail->Password = (string) config('mail.mailers.smtp.password');
            $mail->CharSet = 'UTF-8';

            $encryption = env('MAIL_ENCRYPTION', config('mail.mailers.smtp.scheme'));
            if ($encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($encryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $fromAddress = (string) config('mail.from.address');
            $fromName = (string) config('mail.from.name');

            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($email, $name ?: 'User');
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = view('emails.otp-code', [
                'code' => $otp,
                'expiresInMinutes' => $expiresInMinutes,
                'recipientName' => $name,
            ])->render();
            $mail->AltBody = "Kode D-Voyager Anda: {$otp}. Kode berlaku selama {$expiresInMinutes} menit.";

            $mail->send();
        } catch (PHPMailerException $e) {
            Log::error('PHPMailer OTP failed: ' . $e->getMessage());
            throw new \RuntimeException('Gagal mengirim OTP lewat email. Periksa konfigurasi SMTP.');
        }
    }

    private function frontendRedirectFromState(?string $stateParam): string
    {
        $frontendRedirect = "http://localhost:8100/login";

        if (!$stateParam) {
            return $frontendRedirect;
        }

        $stateData = json_decode(base64_decode($stateParam), true);
        if (isset($stateData["redirect"])) {
            return $stateData["redirect"];
        }

        return $frontendRedirect;
    }

    private function ensureCustomerRecord(User $user): void
    {
        $customer = Customer::withTrashed()->where("user_id", $user->id)->first();

        if ($customer) {
            if ($customer->trashed()) {
                $customer->restore();
            }

            return;
        }

        Customer::create(["user_id" => $user->id]);
    }

    private function googleOAuthErrorMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'invalid_grant')) {
            return 'Login Google gagal karena kode Google sudah kedaluwarsa atau sudah pernah dipakai. Silakan coba login Google ulang.';
        }

        if (str_contains($message, 'redirect_uri_mismatch')) {
            return 'Login Google gagal karena redirect URI belum cocok dengan konfigurasi Google Console.';
        }

        return 'Login Google gagal. Silakan coba lagi.';
    }

    public function logout(Request $request)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(["message" => "Unauthenticated"], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(["message" => "Unauthenticated"], 401);
        }

        $accessToken->delete();
        return response()->json(["message" => "Logged out"]);
    }
}
