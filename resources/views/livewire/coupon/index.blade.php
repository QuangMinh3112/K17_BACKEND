<div class="card mb-4 shadow">
    <div class="card-header py-3 bg-green">
        <h6 class="m-0 font-weight-bold text-white">Danh sách mã giảm giá</h6>
    </div>
    <div class="card-body">
        <div class="row mx-auto justify-content-between">
            <div class="d-flex">
                <div class="mb-3 mr-2">
                    <input type="text" class="form-control" name="" id=""
                        wire:model.live.debounce.300ms ="name" placeholder="Tìm theo tên">
                </div>
                <div class="mb-3 mr-2">
                    <select name="" id="" class="form-control"
                        wire:model.live.debounce.300ms = "status">
                        <option value="public" selected>Công bố</option>
                        <option value="private">Riêng tư</option>
                    </select>
                </div>
                <div class="mb-3 mr-2">
                    <select name="" id="" class="form-control" wire:model.live.debounce.300ms = "type">
                        <option value="" selected>Loại mã giảm giá</option>
                        <option value="percent">%</option>
                        <option value="number">Giá tiền</option>
                        <option value="free_ship">Free Ship</option>
                    </select>
                </div>
                <div class="mb-3 mr-2">
                    <select name="" id="" class="form-control"
                        wire:model.live.debounce.300ms = "is_activate">
                        <option value="1" selected>Hoạt động</option>
                        <option value="0">Ngừng hoạt động</option>
                    </select>
                </div>
                <div class="mb-3 mr-2 align-item-center">
                    @if (session('success'))
                        <span class="text-success">{{ session('success') }}</span>
                    @endif
                </div>
            </div>
            <div>
                <a wire:navigate href="{{ route('coupon.give') }}" class="btn btn-success">Tặng mã giảm giá</a>
            </div>
        </div>
        <div class="example">
            <div class="rounded-bottom">
                <div class="">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th scope="col">Tên</th>
                                <th scope="col">QR CODE</th>
                                <th scope="col">CODE</th>
                                <th scope="col">Số lượng</th>
                                <th scope="col">Đã dùng</th>
                                <th scope="col">Giảm giá</th>
                                <th scope="col">Điểm YC</th>
                                <th scope="col">Giá YC</th>
                                <th scope="col">Ngày bắt đầu</th>
                                <th scope="col">Ngày kết thúc</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody wire:loading.class='op-low'>
                            @foreach ($coupons as $data)
                                <tr>
                                    <th>{{ $data->id }}</th>
                                    <td>{{ $data->name }}</td>
                                    <td class="img-thumb">{!! $data->image !!}</td>
                                    <td>{{ $data->code }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ $data->used_count }}</td>
                                    <td>{{ $data->value }} {{ $data->getCouponValue() }}</td>
                                    <td>{{ $data->point_required }}</td>
                                    <td>{{ $data->price_required }} VNĐ</td>
                                    <td>{{ \Carbon\Carbon::parse($data->start_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($data->end_date)->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="d-flex">
                                            {{-- Sửa --}}
                                            <a wire:navigate href="{{ route('coupon.edit', $data->id) }}"
                                                class="mx-2 text-success"><i class="fa-solid fa-pen-to-square"></i></a>
                                            {{-- Ngừng hoạt động --}}
                                            @if ($is_activate == 1)
                                                <a wire:click='deActivate({{ $data->id }})'
                                                    class="mx-2 text-danger"><i class="fa-solid fa-stop"></i></a>
                                            @else
                                                <a wire:click='activate({{ $data->id }})'
                                                    class="mx-2 text-success"><i class="fa-solid fa-check"></i></a>
                                            @endif
                                            {{-- Để riêng tư --}}
                                            @if ($status === 'public')
                                                <a wire:click='private({{ $data->id }})' class="mx-2 text-danger">
                                                    <i class="fa-solid fa-lock"></i></a>
                                            @else
                                                <a wire:click='public({{ $data->id }})' class="mx-2 text-success">
                                                    <i class="fa-solid fa-unlock"></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="py-1 px-1">
            <div class="d-flex justify-content-between">
                <div class="flex space-x-4 items-center">
                    <label class="w-32 text-sm font-medium text-gray-900">Hiển thị :</label>
                    <select wire:model.live='page'
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="flex space-x-4 items-center">
                    <div class="flex items-center">
                        {{ $coupons->links('Layout.livewire-pagination') }}
                    </div>
                    <span class="ml-4 text-sm text-gray-500">
                        Hiển thị {{ $coupons->firstItem() }} - {{ $coupons->lastItem() }} của
                        {{ $coupons->total() }} kết quả
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
