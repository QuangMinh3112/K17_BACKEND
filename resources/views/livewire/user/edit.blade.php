<div class="">
    <div class="card mb-4 shadow">
        <div class="card-header py-3 bg-green">
            <h6 class="m-0 font-weight-bold text-white">Cập nhật người dùng</h6>
        </div>
        <div class="card-body">
            <div class="example"></div>
            <div class="rounded-bottom">
                <form class="p-3 active" wire:submit.prevent="update">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input class="form-control" type="text" wire:model="name">
                            @error('name')
                                <span class="text-danger fst-italic">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="text" wire:model="email">
                            @error('email')
                                <span class="text-danger fst-italic">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input class="form-control" type="text" wire:model="phone_number">
                            @error('phone_number')
                                <span class="text-danger fst-italic">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input disabled class="form-control" type="password" wire:model="password">
                            @error('password')
                                <span class="text-danger fst-italic">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Vai trò</label>
                            <select class="form-control" wire:model="role">
                                <option value="0" selected>Người dùng</option>
                                <option value="2">Nhân viên</option>
                                <option value="1">Quản lý</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-control" wire:model="status">
                                <option value="1" selected>Hoạt động</option>
                                <option value="0">Bị cấm</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Xác minh tài khoản</label>
                            <select class="form-control" wire:model="is_vertify">
                                <option value="0" selected>Không xác minh</option>
                                <option value="1">Đã xác minh</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <div>
                            <button class="btn btn-success" type="submit">Cập nhật</button>
                            @if (session('success'))
                                <span class="text-success">{{ session('success') }}</span>
                            @endif
                        </div>
                        <a class="btn btn-warning" wire:navigate href="{{ route('user.index') }}">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
