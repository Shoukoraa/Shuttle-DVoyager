# Untuk API LOGIC
from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.shortcuts import render
from .models import ShuttleLocation, Trip, Terminal
from .serializers import ShuttleLocationSerializer, TripSerializer, TerminalSerializer
from django.db.models import Max

@api_view(['GET'])
def active_trips(request):
    # Mengambil semua trip yang statusnya ON_GOING atau SCHEDULED
    trips = Trip.objects.filter(status__in=['ON_GOING', 'SCHEDULED'])
    
    result_data = []
    for trip in trips:
        # Cari lokasi terakhir untuk trip ini
        latest_loc = ShuttleLocation.objects.filter(trip=trip).order_by('-created_at').first()
        
        trip_data = TripSerializer(trip).data
        if latest_loc:
            trip_data['latest_location'] = ShuttleLocationSerializer(latest_loc).data
        else:
            # Jika belum ada tracking, gunakan koordinat origin sebagai posisi awal
            trip_data['latest_location'] = {
                'latitude': trip.origin_lat,
                'longitude': trip.origin_lng
            }
            
        result_data.append(trip_data)

    return Response({
        'status': 'success',
        'data': result_data
    })
    
@api_view(['GET'])
def driver_trips(request):
    driver_name = request.query_params.get('driver_name')
    if not driver_name:
        return Response({'status': 'error', 'message': 'Nama driver diperlukan'}, status=400)
    
    # Ambil semua trip milik driver ini (terjadwal, berjalan, maupun selesai)
    trips = Trip.objects.filter(driver_name=driver_name).order_by('-id')
    serializer = TripSerializer(trips, many=True)
    return Response({'status': 'success', 'data': serializer.data})

@api_view(['POST'])
def update_trip_status(request, trip_id):
    try:
        trip = Trip.objects.get(id=trip_id)
    except Trip.DoesNotExist:
        return Response({'status': 'error', 'message': 'Trip tidak ditemukan'}, status=404)
    
    new_status = request.data.get('status')
    if new_status not in dict(Trip.STATUS_CHOICES):
        return Response({'status': 'error', 'message': 'Status tidak valid'}, status=400)
    
    trip.status = new_status
    trip.save()
    return Response({'status': 'success', 'message': f'Status trip diperbarui menjadi {new_status}'})

@api_view(['POST'])
def update_location(request):
    trip_id = request.data.get('trip_id')
    try:
        trip = Trip.objects.get(id=trip_id)
    except Trip.DoesNotExist:
        return Response({'status': 'error', 'message': 'Trip tidak ditemukan'}, status=404)

    # Validasi: Hanya bisa update lokasi jika status ON_GOING
    if trip.status != 'ON_GOING':
        return Response({
            'status': 'error', 
            'message': f'Tidak dapat memperbarui lokasi. Status trip saat ini: {trip.status}'
        }, status=400)

    data = request.data.copy()
    data['trip'] = trip.id
    
    serializer = ShuttleLocationSerializer(data=data)
    if serializer.is_valid():
        serializer.save()
        return Response({'status': 'success', 'data': serializer.data})

    return Response(serializer.errors, status=400)

@api_view(['POST'])
def reset_trips(request):
    # Mengembalikan semua trip ke status SCHEDULED dan menghapus histori lokasi
    Trip.objects.all().update(status='SCHEDULED')
    ShuttleLocation.objects.all().delete()
    return Response({'status': 'success', 'message': 'Semua trip berhasil direset ke status Terjadwal'})

def map_view(request, trip_id=None):
    return render(request, 'map.html', {'target_trip_id': trip_id})

def admin_view(request):
    return render(request, 'admin.html')

@api_view(['GET'])
def list_all_trips(request):
    trips = Trip.objects.all().order_by('-created_at')
    serializer = TripSerializer(trips, many=True)
    return Response({'status': 'success', 'data': serializer.data})

@api_view(['POST'])
def create_trip(request):
    serializer = TripSerializer(data=request.data)
    if serializer.is_valid():
        serializer.save()
        return Response({'status': 'success', 'message': 'Jadwal berhasil dibuat', 'data': serializer.data})
    return Response(serializer.errors, status=400)

@api_view(['GET'])
def list_available_drivers(request):
    drivers = Trip.objects.values_list('driver_name', flat=True).distinct()
    return Response({'status': 'success', 'data': list(drivers)})

def driver_view(request):
    return render(request, 'driver.html')

# --- Terminal Management Views ---

from django.views.decorators.csrf import csrf_exempt

def terminal_management_view(request):
    return render(request, 'terminals.html')

@csrf_exempt
@api_view(['GET', 'POST'])
def terminal_list_create(request):
    if request.method == 'GET':
        terminals = Terminal.objects.all().order_by('-created_at')
        serializer = TerminalSerializer(terminals, many=True)
        return Response({'status': 'success', 'data': serializer.data})
    
    elif request.method == 'POST':
        serializer = TerminalSerializer(data=request.data)
        if serializer.is_valid():
            serializer.save()
            return Response({'status': 'success', 'message': 'Terminal berhasil disimpan', 'data': serializer.data})
        return Response(serializer.errors, status=400)

@csrf_exempt
@api_view(['DELETE'])
def terminal_delete(request, pk):
    try:
        terminal = Terminal.objects.get(pk=pk)
        terminal.delete()
        return Response({'status': 'success', 'message': 'Terminal berhasil dihapus'})
    except Terminal.DoesNotExist:
        return Response({'status': 'error', 'message': 'Terminal tidak ditemukan'}, status=404)