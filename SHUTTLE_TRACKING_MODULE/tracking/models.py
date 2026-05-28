# Untuk tabel database
from django.db import models

class Vehicle(models.Model):
    plate_number = models.CharField(max_length=20, unique=True, verbose_name="Plat Nomor")
    model_name = models.CharField(max_length=50, verbose_name="Model Kendaraan")
    capacity = models.IntegerField(default=10, verbose_name="Kapasitas Kursi")

    def __str__(self):
        return f"{self.plate_number} ({self.model_name})"

class Trip(models.Model):
    STATUS_CHOICES = [
        ('SCHEDULED', 'Terjadwal'),
        ('ON_GOING', 'Dalam Perjalanan'),
        ('COMPLETED', 'Selesai'),
    ]

    vehicle = models.ForeignKey(Vehicle, on_delete=models.SET_NULL, null=True, blank=True, related_name='trips')
    vehicle_number = models.CharField(max_length=20, verbose_name="Nomor Kendaraan")
    driver_name = models.CharField(max_length=100, verbose_name="Nama Driver")
    origin_name = models.CharField(max_length=100, verbose_name="Titik Keberangkatan")
    origin_lat = models.FloatField(default=-6.3071, verbose_name="Latitude Asal")
    origin_lng = models.FloatField(default=107.2947, verbose_name="Longitude Asal")
    destination_name = models.CharField(max_length=100, verbose_name="Titik Tujuan")
    destination_lat = models.FloatField(verbose_name="Latitude Tujuan")
    destination_lng = models.FloatField(verbose_name="Longitude Tujuan")
    
    departure_time = models.DateTimeField(null=True, blank=True, verbose_name="Waktu Keberangkatan")
    status = models.CharField(max_length=20, choices=STATUS_CHOICES, default='SCHEDULED')
    
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"{self.vehicle_number} - {self.driver_name} ({self.origin_name} ke {self.destination_name})"

class ShuttleLocation(models.Model):
    trip = models.ForeignKey(Trip, on_delete=models.CASCADE, related_name='locations', null=True)
    trip_id_old = models.IntegerField(null=True, blank=True) # Untuk migrasi data lama jika perlu
    latitude = models.FloatField()
    longitude = models.FloatField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Lokasi Trip {self.trip.id if self.trip else self.trip_id_old} pada {self.created_at}"

class Terminal(models.Model):
    name = models.CharField(max_length=150, verbose_name="Nama Terminal/Halte")
    city = models.CharField(max_length=100, verbose_name="Kota")
    latitude = models.FloatField(verbose_name="Latitude")
    longitude = models.FloatField(verbose_name="Longitude")
    address = models.TextField(verbose_name="Alamat Lengkap")
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.name} ({self.city})"

    

