@section('title', 'Migration')
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">
            <div class="header row">
                <div class="col-md-2">
                    <input type="text" class="form-control" wire:model="keyword" placeholder="Searching..." />
                </div>
                <div class="col-md-3">
                    <a href="javascript:void(0)" class="btn btn-primary" data-toggle="modal" data-target="#modal_upload"><i class="fa fa-plus"></i> Upload Konven</a>
                    <a href="javascript:void(0)" class="btn btn-info" data-toggle="modal" data-target="#modal_upload_syariah"><i class="fa fa-plus"></i> Upload Syariah</a>
                </div>
                <div class="col-md-2">
                    <span wire:loading>
                        <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                        <span class="sr-only">{{ __('Loading...') }}</span>
                    </span>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-b-0 c_list">
                        <thead>
                            <tr>
                                <th>No</th>
                                @foreach(\Illuminate\Support\Facades\Schema::getColumnListing('migration_data') as $column)
                                    @if($column=='id' || $column=='updated_at') @continue @endif
                                    <th>{{$column}}</th>
                                @endforeach
                                <th>Updated</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $k => $item)
                            <tr>
                                <td style="width: 50px;">{{$k+1}}</td>
                                @foreach(\Illuminate\Support\Facades\Schema::getColumnListing('migration_data') as $column)
                                    @if($column=='id' || $column=='updated_at') @continue @endif
                                    <td>{{$item->$column}}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <br />
                {{$data->links()}}
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="modal_upload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="" wire:submit.prevent="upload">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-upload"></i> Upload</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true close-btn">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="file" wire:model="file" />
                        @error('name')
                            <ul class="parsley-errors-list filled" id="parsley-id-29"><li class="parsley-required">{{ $message }}</li></ul>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <span wire:loading wire:target="upload">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                            <span class="sr-only">{{ __('Loading...') }}</span>
                        </span>
                        <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">No</button>
                        <button type="submit" wire:loading.remove class="btn btn-danger">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" wire:ignore.self id="modal_upload_syariah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="" wire:submit.prevent="upload_syariah">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-upload"></i> Upload</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true close-btn">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="file" wire:model="file_syariah" />
                        @error('name')
                            <ul class="parsley-errors-list filled" id="parsley-id-29"><li class="parsley-required">{{ $message }}</li></ul>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <span wire:loading wire:target="upload">
                            <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i>
                            <span class="sr-only">{{ __('Loading...') }}</span>
                        </span>
                        <button type="button" class="btn btn-secondary close-btn" data-dismiss="modal">No</button>
                        <button type="submit" wire:loading.remove class="btn btn-danger">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>