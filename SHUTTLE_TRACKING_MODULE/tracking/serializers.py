# Inti rest API Django
from rest_framework import serializers
from .models import ShuttleLocation, Trip, Vehicle, Terminal

class VehicleSerializer(serializers.ModelSerializer):
    class Meta:
        model = Vehicle
        fields = '__all__'

class TripSerializer(serializers.ModelSerializer):
    vehicle_details = VehicleSerializer(source='vehicle', read_only=True)
    latest_location = serializers.SerializerMethodField()
    
    class Meta:
        model = Trip
        fields = '__all__'

    def get_latest_location(self, obj):
        loc = obj.locations.order_by('-created_at').first()
        if loc:
            return {
                'latitude': loc.latitude,
                'longitude': loc.longitude,
                'created_at': loc.created_at
            }
        return None

class ShuttleLocationSerializer(serializers.ModelSerializer):
    class Meta:
        model = ShuttleLocation
        fields = '__all__'

class TerminalSerializer(serializers.ModelSerializer):
    class Meta:
        model = Terminal
        fields = '__all__'
