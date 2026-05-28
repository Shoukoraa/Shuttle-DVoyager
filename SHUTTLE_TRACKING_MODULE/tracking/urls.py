from django.urls import path
from .views import (
    update_location,
    map_view,
    active_trips,
    driver_trips,
    update_trip_status,
    driver_view,
    reset_trips,
    admin_view,
    list_all_trips,
    create_trip,
    list_available_drivers,
    terminal_management_view,
    terminal_list_create,
    terminal_delete
)

urlpatterns = [
    path('update-location/', update_location),
    path('map/', map_view),
    path('map/<int:trip_id>/', map_view),
    path('active-trips/', active_trips),
    path('reset-trips/', reset_trips),
    
    # Admin Routes
    path('admin/', admin_view),
    path('admin/trips/', list_all_trips),
    path('admin/create-trip/', create_trip),
    
    # Driver Routes
    path('driver/', driver_view),
    path('available-drivers/', list_available_drivers),
    path('driver-trips/', driver_trips),
    path('trip/<int:trip_id>/status/', update_trip_status),
    
    # Terminal Management Routes
    path('terminals/', terminal_management_view),
    path('terminals/data/', terminal_list_create),
    path('terminals/data/<int:pk>/', terminal_delete),
]
