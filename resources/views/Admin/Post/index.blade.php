{{-- MẪU --}}
@section('title', 'Danh sách bài đăng')
@extends('Admin.Layouts.layout')
@section('content')
    <div class="row">
        <div class="col-4">
            <div class="my-2 d-flex">
                <x-button.add-btn :route="'admin.post.create'" />
                <a class="btn btn-outline-dark" href="{{ route('admin.book.archive') }}"><i
                        class="fa-solid fa-trash"></i></i></a>
            </div>
        </div>
        <div class="col-8">
            <div class="my-2">
                <form class="d-flex justify-content-end gap-2" method="POST" action="{{ route('admin.book.search') }}">
                    @csrf
                    <select class="form-select" style="width: 25%" name="category_id">
                        @include('Admin.partials.category-option')
                    </select>
                    <select class="form-select" style="width: 25%" name="status">
                        <option selected disabled>Trạng thái</option>
                        <option value="Công bố" {{ session('status') == 'Công bố' ? 'selected' : '' }}>Công bố</option>
                        <option value="Bản nháp" {{ session('status') == 'Bản nháp' ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                    <div class="">
                        <input type="text" name="name" class="form-control" placeholder="Tên"
                            value="{{ session('name') }}">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="card mb-4 shadow">
        <div class="card-header bg-dark text-white">
            <h2>Danh sách bài đăng</h2>
        </div>
        <div class="card-body">
            <div class="example">
                <div class="rounded-bottom">
                    <div class="p-3">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th scope="col">Tên bài</th>
                                    <th scope="col">Danh mục</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Hình ảnh</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($posts) > 0)
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach ($posts as $data)
                                        <tr>
                                            <th>{{ $i }}</th>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->getCategoryName() }}</td>
                                            <td>{{ $data->status }}</td>
                                            <td><img src="{{ asset('storage/' . $data->image) }}" alt=""
                                                    height="100px">
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <!-- Nút View -->
                                                    <x-button.view-btn :route="'admin.post.show'" :id="$data->id" />
                                                    {{-- Sửa --}}
                                                    <x-button.edit-btn :route="'admin.post.edit'" :id="$data->id" />
                                                    {{-- Xoá --}}
                                                    <x-button.soft-del-btn :route="'admin.book.delete'" :id="$data->id" />
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection