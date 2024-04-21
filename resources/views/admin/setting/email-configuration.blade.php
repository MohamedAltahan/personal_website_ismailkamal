<div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.stmp-setting-update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <x-form.input class="form-control" name="sender_email" label='Sender email'
                        value="{{ $emailSettings->sender_email }}" />
                </div>

                <div class="form-group">
                    <x-form.input class="form-control" name="host" label='Mail Host'
                        value="{{ $emailSettings->host }}" />
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-form.input class="form-control" name="username" label='Stmp username'
                                value="{{ $emailSettings->username }}" />
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <x-form.input class="form-control" name="password" label='Stmp password'
                                value="{{ $emailSettings->password }}" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-form.input class="form-control" name="port" label='Mail port'
                                value="{{ $emailSettings->port }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="">Mail enctyption</label>
                            <select name="encryption" id="" class="form-control ">
                                <option @selected($emailSettings->encryption == 'tls') value="tls">TLS</option>
                                <option @selected($emailSettings->encryption == 'ssl') value="ssl">SSL</option>
                            </select>
                            <x-form.error name='time_zone' message={{ $message }} />
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
