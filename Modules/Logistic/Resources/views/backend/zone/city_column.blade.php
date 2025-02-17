@forelse ($data->cities as $city)
    <span class="badge bg-secondary rounded-pill">{{ $city->name }}</span>
@empty
    <span class="badge bg-secondary rounded-pill">N/A</span>
@endforelse
