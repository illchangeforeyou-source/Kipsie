<form action="{{ route('user.access.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    @foreach($pages as $page)
    <div>
        <label>
            <input type="checkbox" name="pages[]" value="{{ $page->id }}"
    {{ $user->pages->contains($page->id) ? 'checked' : '' }}>

        </label>
    </div>
@endforeach

    <button type="submit">Save Access</button>
</form>
