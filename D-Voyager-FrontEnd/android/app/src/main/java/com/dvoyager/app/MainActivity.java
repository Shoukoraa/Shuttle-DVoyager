package com.dvoyager.app;

import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.AnimatorSet;
import android.animation.ObjectAnimator;
import android.os.Bundle;
import android.view.View;
import android.view.animation.AccelerateInterpolator;
import androidx.core.splashscreen.SplashScreen;
import com.getcapacitor.BridgeActivity;

public class MainActivity extends BridgeActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        // Install the splash screen
        SplashScreen splashScreen = SplashScreen.installSplashScreen(this);

        super.onCreate(savedInstanceState);

        // Implement the "Zoom & Reveal" animation
        splashScreen.setOnExitAnimationListener(splashScreenView -> {
            final View view = splashScreenView.getView();
            final View icon = splashScreenView.getIconView();

            // Create zoom in effect for the icon (making it larger as it fades)
            ObjectAnimator scaleX = ObjectAnimator.ofFloat(icon, View.SCALE_X, 1f, 4f);
            ObjectAnimator scaleY = ObjectAnimator.ofFloat(icon, View.SCALE_Y, 1f, 4f);

            // Create fade out effect for the whole splash screen background
            ObjectAnimator alpha = ObjectAnimator.ofFloat(view, View.ALPHA, 1f, 0f);

            // Play all animations together
            AnimatorSet animatorSet = new AnimatorSet();
            animatorSet.setDuration(600L);
            animatorSet.setInterpolator(new AccelerateInterpolator());
            animatorSet.playTogether(scaleX, scaleY, alpha);

            // Remove the splash screen view once the animation is finished
            animatorSet.addListener(new AnimatorListenerAdapter() {
                @Override
                public void onAnimationEnd(Animator animation) {
                    splashScreenView.remove();
                }
            });

            // Start the animation
            animatorSet.start();
        });
    }
}
