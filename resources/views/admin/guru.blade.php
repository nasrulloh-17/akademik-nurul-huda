@extends('layouts.dashboard')

@section('judul_halaman', 'Guru')

@section('konten')
<form class="card" method="post" action="{{ route('admin.guru.simpan') }}" data-guru-role-form>
    @csrf

    <div class="form-grid">
        <input name="nama_guru" placeholder="Nama Guru" required>
        <label>
            Tanggal Lahir
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
        </label>
        <select name="jenis_kelamin" required>
            <option value="">Pilih jenis kelamin</option>
            <option value="Laki-laki" @selected(old('jenis_kelamin') === 'Laki-laki')>Laki-laki</option>
            <option value="Perempuan" @selected(old('jenis_kelamin') === 'Perempuan')>Perempuan</option>
        </select>
        <input type="password" name="kata_sandi" placeholder="Password" required>
        <input name="telepon" placeholder="Telepon">
    </div>

    <p>
        <textarea name="alamat" placeholder="Alamat"></textarea>
    </p>

    <p>
        <label>
            <input
                type="checkbox"
                name="role[]"
                value="pengampu mata pelajaran"
                data-role="pengampu mata pelajaran"
                style="width:auto"
                {{ in_array('pengampu mata pelajaran', old('role', []), true) ? 'checked' : '' }}
            >
            Pengampu Mata Pelajaran
        </label>

        <label>
            <input
                type="checkbox"
                name="role[]"
                value="wali kelas"
                data-role="wali kelas"
                style="width:auto"
                {{ in_array('wali kelas', old('role', []), true) ? 'checked' : '' }}
            >
            Wali Kelas
        </label>

        <label>
            <input
                type="checkbox"
                name="role[]"
                value="staff"
                data-role="staff"
                style="width:auto"
                {{ in_array('staff', old('role', []), true) ? 'checked' : '' }}
            >
            Staff
        </label>
    </p>

    <div class="form-grid">
        <label data-role-detail="wali kelas">
            Wali Kelas
            <select name="wali_kelas_id">
                <option value="">Pilih kelas wali</option>
                @foreach($kelas as $item)
                    <option value="{{ $item->id }}" {{ old('wali_kelas_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </label>

        <label data-role-detail="staff">
            Jenis Staff
            <select name="staff_jenis">
                <option value="">Pilih jenis staff</option>
                <option value="staff TU" {{ old('staff_jenis') === 'staff TU' ? 'selected' : '' }}>
                    Staff TU
                </option>
                <option value="staff keuangan" {{ old('staff_jenis') === 'staff keuangan' ? 'selected' : '' }}>
                    Staff Keuangan
                </option>
            </select>
        </label>
    </div>

    @if($errors->any())
        <div class="alert" style="margin-top:16px">
            {{ $errors->first() }}
        </div>
    @endif

    <button class="btn">Simpan Guru</button>
</form>

<table>
    <tr>
        <th>ID Guru</th>
        <th>Nama Guru</th>
        <th>Tanggal Lahir</th>
        <th>Jenis Kelamin</th>
        <th>Role</th>
        <th>Telepon</th>
        <th>Ubah Password</th>
        <th>Aksi</th>
    </tr>

    @foreach($guru as $item)
        @php($roleItems = $roles[$item->id] ?? collect())
        @php($roleNames = $roleItems->pluck('role')->toArray())
        @php($waliRole = $roleItems->firstWhere('role', 'wali kelas'))
        @php($staffRole = $roleItems->firstWhere('role', 'staff'))

        <tr>
            <td>{{ $item->id_guru }}</td>
            <td>{{ $item->nama_guru }}</td>
            <td>{{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d-m-Y') : '-' }}</td>
            <td>{{ $item->jenis_kelamin ?? '-' }}</td>
            <td>
                @if($roleItems->isNotEmpty())
                    @foreach($roleItems as $role)
                        <div>
                            {{ ucwords($role->role) }}

                            @if($role->role === 'wali kelas' && $role->nama_kelas)
                                {{ $role->nama_kelas }}
                            @endif

                            @if($role->role === 'staff' && $role->staff_jenis)
                                - {{ ucwords($role->staff_jenis) }}
                            @endif
                        </div>
                    @endforeach
                @else
                    -
                @endif
            </td>
            <td>{{ $item->telepon }}</td>
            <td>
                <form method="post" action="{{ route('admin.guru.password', $item->id) }}">
                    @csrf

                    <div style="display:flex;gap:8px">
                        <input type="password" name="kata_sandi" placeholder="Password baru" required>
                        <button class="btn">Ubah</button>
                    </div>
                </form>
            </td>
            <td>
                <details style="margin-bottom:10px">
                    <summary class="btn alt" style="display:inline-block">Ubah Data</summary>

                    <form method="post" action="{{ route('admin.guru.ubah', $item->id) }}" style="margin-top:12px;min-width:320px" data-guru-role-form>
                        @csrf

                        <div class="form-grid">
                            <input name="nama_guru" value="{{ $item->nama_guru }}" placeholder="Nama Guru" required>
                            <label>
                                Tanggal Lahir
                                <input
                                    type="date"
                                    name="tanggal_lahir"
                                    value="{{ $item->tanggal_lahir }}"
                                    required
                                >
                            </label>
                            <select name="jenis_kelamin" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-laki" @selected($item->jenis_kelamin === 'Laki-laki')>
                                    Laki-laki
                                </option>
                                <option value="Perempuan" @selected($item->jenis_kelamin === 'Perempuan')>
                                    Perempuan
                                </option>
                            </select>
                            <input name="telepon" value="{{ $item->telepon }}" placeholder="Telepon">
                        </div>

                        <p class="muted">
                            ID guru saat ini: <strong>{{ $item->id_guru }}</strong>.
                            Jika tanggal lahir diubah, ID guru akan dibuat ulang otomatis.
                        </p>

                        <p>
                            <textarea name="alamat" placeholder="Alamat">{{ $item->alamat }}</textarea>
                        </p>

                        <p>
                            <label>
                                <input
                                    type="checkbox"
                                    name="role[]"
                                    value="pengampu mata pelajaran"
                                    data-role="pengampu mata pelajaran"
                                    style="width:auto"
                                    {{ in_array('pengampu mata pelajaran', $roleNames, true) ? 'checked' : '' }}
                                >
                                Pengampu Mata Pelajaran
                            </label>

                            <label>
                                <input
                                    type="checkbox"
                                    name="role[]"
                                    value="wali kelas"
                                    data-role="wali kelas"
                                    style="width:auto"
                                    {{ in_array('wali kelas', $roleNames, true) ? 'checked' : '' }}
                                >
                                Wali Kelas
                            </label>

                            <label>
                                <input
                                    type="checkbox"
                                    name="role[]"
                                    value="staff"
                                    data-role="staff"
                                    style="width:auto"
                                    {{ in_array('staff', $roleNames, true) ? 'checked' : '' }}
                                >
                                Staff
                            </label>
                        </p>

                        <div class="form-grid">
                            <label data-role-detail="wali kelas">
                                Wali Kelas
                                <select name="wali_kelas_id">
                                    <option value="">Pilih kelas wali</option>
                                    @foreach($kelas as $kelasItem)
                                        <option value="{{ $kelasItem->id }}" @selected(optional($waliRole)->kelas_id === $kelasItem->id)>
                                            {{ $kelasItem->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label data-role-detail="staff">
                                Jenis Staff
                                <select name="staff_jenis">
                                    <option value="">Pilih jenis staff</option>
                                    <option value="staff TU" @selected(optional($staffRole)->staff_jenis === 'staff TU')>
                                        Staff TU
                                    </option>
                                    <option value="staff keuangan" @selected(optional($staffRole)->staff_jenis === 'staff keuangan')>
                                        Staff Keuangan
                                    </option>
                                </select>
                            </label>
                        </div>

                        <p>
                            <button class="btn">Simpan Perubahan</button>
                        </p>
                    </form>
                </details>

                <form method="post" action="{{ route('admin.guru.hapus', $item->id) }}">
                    @csrf
                    <button class="btn danger">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

<script>
    document.querySelectorAll('[data-guru-role-form]').forEach((form) => {
        const roleWaliKelas = form.querySelector('[data-role="wali kelas"]');
        const roleStaff = form.querySelector('[data-role="staff"]');
        const detailWaliKelas = form.querySelector('[data-role-detail="wali kelas"]');
        const detailStaff = form.querySelector('[data-role-detail="staff"]');

        const toggleDetailRole = () => {
            detailWaliKelas.style.display = roleWaliKelas.checked ? 'block' : 'none';
            detailStaff.style.display = roleStaff.checked ? 'block' : 'none';
        };

        roleWaliKelas.addEventListener('change', toggleDetailRole);
        roleStaff.addEventListener('change', toggleDetailRole);
        toggleDetailRole();
    });
</script>
@endsection
