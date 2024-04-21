<div class="form-group">
    <label for="">Main Categories</label>
    <select name="category_id" class="form-control">
        <option value="">Select main category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }} "@selected(old('category_id', $subCategory->category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <x-form.input name="name" label="Name" class="form-control" value="{{ $subCategory->name }}" />
</div>

<div class="form-group">
    <label for="">status</label>
    <select name="status" class="form-control">
        <option value="active" @selected($subCategory->status == 'active')>Active</option>
        <option value="inactive" @selected($subCategory->status == 'inactive')>Inactive</option>
    </select>
</div>

<button type="submit" class="btn btn-primary">{{ $buttonLabel ?? 'Create' }}</button>
