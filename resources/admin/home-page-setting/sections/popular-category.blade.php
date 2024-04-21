@php
    $popularCategorySection = json_decode($popularCategorySection->value);
@endphp
<div class="tab-pane fade show active" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
    <div class="card border">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label for="status1" style="color: red">show (popular category ) on the Home
                        page:</label>
                </div>
                <div class="col-md-1 ">
                    <label class="custom-switch ">
                        <input type="checkbox" name="status" id='status1' data-sectionname="popularCategory"
                            class="custom-switch-input change-status"
                            {{ @$sectionStatus->popularCategory == 'active' ? 'checked' : '' }}>
                        <span class="custom-switch-indicator"></span>
                    </label>
                </div>
            </div>
            <form action="{{ route('admin.popular-category-section') }}" method="POST">
                @csrf
                @method('PUT')

                <h5>Category (1)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Category</label>
                            <select name="main_category_one" id="" class="form-control main-category">
                                <option value="">Select </option>
                                @foreach ($categories as $category)
                                    <option @selected($category->id == $popularCategorySection[0]->main_category) value="{{ $category->id }}">
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='main_category_one' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $subCategories = App\Models\SubCategory::where(
                                    'category_id',
                                    $popularCategorySection[0]->main_category,
                                )->get();
                            @endphp
                            <label for="">Sub Category</label>
                            <select name="sub_category_one" id="" class="form-control sub-category">
                                <option value="">Select </option>
                                @foreach ($subCategories as $subCategory)
                                    <option @selected($subCategory->id == $popularCategorySection[0]->sub_category) value="{{ $subCategory->id }}">
                                        {{ $subCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='sub_category_one' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $childCategories = App\Models\ChildCategory::where(
                                    'sub_category_id',
                                    $popularCategorySection[0]->sub_category,
                                )->get();
                            @endphp
                            <label for="">Child Category</label>
                            <select name="child_category_one" id="" class="form-control child-category">
                                <option value="">Select </option>
                                @foreach ($childCategories as $childCategory)
                                    <option @selected($childCategory->id == $popularCategorySection[0]->child_category) value="{{ $childCategory->id }}">
                                        {{ $childCategory->name }}</option>
                                @endforeach
                            </select>
                            </select>
                            <x-form.error name='child_category_one' message={{ $message }} />
                        </div>
                    </div>
                </div>

                <h5>Category (2)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="main_category_two" id="" class="form-control main-category">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option @selected($category->id == $popularCategorySection[1]->main_category) value="{{ $category->id }}">
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='main_category_two' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $subCategories = App\Models\SubCategory::where(
                                    'category_id',
                                    $popularCategorySection[1]->main_category,
                                )->get();
                            @endphp
                            <label for="">Sub Category</label>
                            <select name="sub_category_two" id="" class="form-control sub-category">
                                <option value="">Select </option>
                                @foreach ($subCategories as $subCategory)
                                    <option @selected($subCategory->id == $popularCategorySection[1]->sub_category) value="{{ $subCategory->id }}">
                                        {{ $subCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='sub_category_two' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $childCategories = App\Models\ChildCategory::where(
                                    'sub_category_id',
                                    $popularCategorySection[1]->sub_category,
                                )->get();
                            @endphp
                            <label for="">Child Category</label>
                            <select name="child_category_two" id="" class="form-control child-category">
                                <option value="">Select </option>
                                @foreach ($childCategories as $childCategory)
                                    <option @selected($childCategory->id == $popularCategorySection[1]->child_category) value="{{ $childCategory->id }}">
                                        {{ $childCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='child_category_two' message={{ $message }} />
                        </div>
                    </div>
                </div>

                <h5>Category (3)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="main_category_three" id="" class="form-control main-category">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option @selected($category->id == $popularCategorySection[2]->main_category) value="{{ $category->id }}">
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='sub_category_three' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $subCategories = App\Models\SubCategory::where(
                                    'category_id',
                                    $popularCategorySection[2]->main_category,
                                )->get();
                            @endphp
                            <label for="">Sub Category</label>
                            <select name="sub_category_three" id="" class="form-control sub-category">
                                <option value="">Select </option>
                                @foreach ($subCategories as $subCategory)
                                    <option @selected($subCategory->id == $popularCategorySection[2]->sub_category) value="{{ $subCategory->id }}">
                                        {{ $subCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='sub_category_three' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $childCategories = App\Models\ChildCategory::where(
                                    'sub_category_id',
                                    $popularCategorySection[2]->sub_category,
                                )->get();
                            @endphp
                            <label for="">Child Category</label>
                            <select name="child_category_three" id="" class="form-control child-category">
                                <option value="">Select </option>
                                @foreach ($childCategories as $childCategory)
                                    <option @selected($childCategory->id == $popularCategorySection[2]->child_category) value="{{ $childCategory->id }}">
                                        {{ $childCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='child_category_three' message={{ $message }} />
                        </div>
                    </div>
                </div>

                <h5>Category (4)</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="">Category</label>
                            <select name="main_category_four" id="" class="form-control main-category">
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option @selected($category->id == $popularCategorySection[3]->main_category) value="{{ $category->id }}">
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='main_category_four' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $subCategories = App\Models\SubCategory::where(
                                    'category_id',
                                    $popularCategorySection[3]->main_category,
                                )->get();
                            @endphp
                            <label for="">Sub Category</label>
                            <select name="sub_category_four" id="" class="form-control sub-category">
                                <option value="">Select </option>
                                @foreach ($subCategories as $subCategory)
                                    <option @selected($subCategory->id == $popularCategorySection[3]->sub_category) value="{{ $subCategory->id }}">
                                        {{ $subCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='sub_category_four' message={{ $message }} />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @php
                                $childCategories = App\Models\ChildCategory::where(
                                    'sub_category_id',
                                    $popularCategorySection[3]->sub_category,
                                )->get();
                            @endphp
                            <label for="">Child Category</label>
                            <select name="child_category_four" id="" class="form-control child-category">
                                <option value="">Select </option>
                                @foreach ($childCategories as $childCategory)
                                    <option @selected($childCategory->id == $popularCategorySection[3]->child_category) value="{{ $childCategory->id }}">
                                        {{ $childCategory->name }}</option>
                                @endforeach
                            </select>
                            <x-form.error name='child_category_four' message={{ $message }} />
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
{{-- scripts------------------------------------------------------------------------ --}}
@push('scripts')
    <script>
        $(document).ready(function() {
            //get sub categories and assigen them to sub category field=====================
            $('body').on('change', '.main-category', function(e) {
                let id = $(this).val();
                let row = $(this).closest('.row');
                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.get-sub-categories') }}',
                    data: {
                        id
                    },
                    success: function(data) {
                        let subCategoryInput = row.find('.sub-category');
                        subCategoryInput.html(
                            `<option value="">Select sub category</option>`);
                        $.each(data, function(index, item) {
                            subCategoryInput.append(
                                `<option value="${item.id}">${item.name}</option>`);
                        })
                    },
                    error: function() {
                        alert("e");
                    }
                })
            })

            //get child categories--------------------------------------------------
            $('body').on('change', '.sub-category', function(e) {
                let id = $(this).val();
                let row = $(this).closest('.row');

                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.product.get-child-categories') }}',
                    data: {
                        id
                    },
                    success: function(data) {
                        let childCategoryInput = row.find('.child-category');
                        // clear field before add new data
                        childCategoryInput.html(
                            `<option value="">Select child category</option>`);
                        if (Object.values(data).length === 0) {
                            childCategoryInput.append(
                                `<option value="">No child category</option>`
                            );
                        } else {
                            $.each(data, function(index, item) {
                                childCategoryInput.append(
                                    `<option value="${item.id}">${item.name}</option>`
                                );
                            })
                        }

                    },
                    error: function() {
                        alert("Error");
                    }
                })
            })
        })
    </script>
@endpush
