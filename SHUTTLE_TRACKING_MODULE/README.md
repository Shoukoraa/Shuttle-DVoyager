# 🚀 Shuttle Tracking & Map Module (Django Microservice)

Modul ini adalah **Core Engine** untuk sistem pelacakan (Tracking) dan visualisasi peta (Map) yang didesain untuk diintegrasikan dengan aplikasi Laravel (Dashboard Admin) dan Ionic (Driver Mobile).

## 🛠️ Fitur Utama
1. **Multi-Shuttle Live Tracking:** Pelacakan banyak armada secara real-time dengan pergerakan yang mulus (Smooth Interpolation).
2. **Premium Navigation UI:** Tampilan navigasi 3D ala Google Maps untuk Driver.
3. **Master Simulator:** Fitur untuk mensimulasikan pergerakan seluruh armada sekaligus untuk keperluan demo/testing.
4. **Traffic & 3D Mode:** Visualisasi lalu lintas aktual dan gedung 3D menggunakan Mapbox GL JS.

## 📦 Isi Folder
- `tracking/`: Berisi logika API, Model GIS, dan Template Peta.
- `config/`: Pengaturan Django.
- `manage.py`: Script untuk menjalankan server.

## 🚀 Cara Menjalankan
1. Pastikan Python sudah terinstal.
2. Buka terminal di folder ini dan jalankan:
   ```bash
   pip install -r requirements.txt
   python manage.py migrate
   python manage.py runserver 8000
   ```

## 🔗 Panduan Integrasi (Untuk Tim Laravel & Ionic)

### A. Integrasi ke Dashboard Admin (Laravel)
Cukup gunakan **iframe** untuk menampilkan peta live ke dalam dashboard buatan Anda:
```html
<iframe src="http://127.0.0.1:8000/api/admin/" width="100%" height="600px" frameborder="0"></iframe>
```

### B. Integrasi ke Aplikasi Driver (Ionic)
Gunakan **WebView** atau **iframe** di Ionic untuk menampilkan navigasi navigasi:
```html
<iframe src="http://127.0.0.1:8000/api/driver/" width="100%" height="100%" frameborder="0"></iframe>
```

### C. Dokumentasi API (JSON)
Gunakan endpoint berikut untuk komunikasi data:
- **Update Lokasi (Dari Ionic):** `POST /api/update-location/`
  - Body: `{ "trip_id": 1, "latitude": -6.123, "longitude": 106.123 }`
- **Ambil Posisi Armada (Ke Laravel):** `GET /api/active-trips/`
- **Ubah Status Trip:** `POST /api/trip/<id>/status/`
  - Body: `{ "status": "ON_GOING" }`

---
**Dibuat oleh Specialist Map Kelompok Anda**
