<div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.general-setting-update.index') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <x-form.input class="form-control" name="site_name" label='Site Name'
                        value="{{ @$setting->site_name }}" />
                </div>

                <div class="form-group">
                    <x-form.input class="form-control" name="contact_email"
                        label='Contact Email (appear in contact page)' value="{{ @$setting->contact_email }}" />
                </div>

                <div class="form-group">
                    <x-form.input class="form-control" name="contact_phone"
                        label='Contact phone (appear in contact page)' value="{{ @$setting->contact_phone }}" />
                </div>

                <div class="form-group">
                    <x-form.input class="form-control" name="contact_address"
                        label='Contact address (appear in contact page)' value="{{ @$setting->contact_address }}" />
                </div>
                <div class="form-group">
                    <x-form.input class="form-control" name="map" label='map on google (appear in contact page)'
                        value="{{ @$setting->map }}" />
                </div>

                <div class="form-group">
                    <x-form.input class="form-control" name="currency_symbol" label='Currency_symbol'
                        value="{{ @$setting->currency_symbol }}" />
                </div>

                <div class="form-group">
                    <label for="">Default Currency</label>
                    <select name="currency" id="" class="form-control select2">
                        <option value="">Select Currenct</option>
                        @foreach (config('setting.currency_list') as $currency => $symbol)
                            <option @selected(@$setting->currency == $symbol) value="{{ $symbol }}">{{ $currency }}
                                ({{ $symbol }})
                            </option>
                        @endforeach
                    </select>
                    <x-form.error name='currency' message={{ $message }} />
                </div>

                <div class="form-group">
                    <label for="">Layout direction</label>
                    <select name="layout" id="" class="form-control ">
                        <option @selected(@$setting->layout == 'ltr') value="ltr">LTR</option>
                        <option @selected(@$setting->layout == 'rtl') value="rtl">RTL</option>
                    </select>
                    <x-form.error name='layout' message={{ $message }} />
                </div>

                <div class="form-group">
                    <label for="">Time Zone</label>
                    <select name="time_zone" id="" class="form-control select2">
                        <option value="">Select Time Zone</option>
                        @foreach (config('setting.time_zone') as $timeZone => $value)
                            <option @selected(@$setting->time_zone == $timeZone) value="{{ $timeZone }}">{{ $timeZone }}
                            </option>
                        @endforeach
                    </select>
                    <x-form.error name='time_zone' message={{ $message }} />
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@push('styles')
@endpush
