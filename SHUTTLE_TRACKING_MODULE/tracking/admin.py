from django.contrib import admin
from .models import Vehicle, Trip, ShuttleLocation, Terminal

@admin.register(Terminal)
class TerminalAdmin(admin.ModelAdmin):
    list_display = ('name', 'city', 'latitude', 'longitude', 'created_at')
    search_fields = ('name', 'city')

@admin.register(Vehicle)
class VehicleAdmin(admin.ModelAdmin):
    list_display = ('plate_number', 'model_name', 'capacity')

@admin.register(Trip)
class TripAdmin(admin.ModelAdmin):
    list_display = ('vehicle_number', 'driver_name', 'origin_name', 'destination_name', 'status')

@admin.register(ShuttleLocation)
class ShuttleLocationAdmin(admin.ModelAdmin):
    list_display = ('trip', 'latitude', 'longitude', 'created_at')
