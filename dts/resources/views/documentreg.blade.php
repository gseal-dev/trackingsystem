@extends('layout')
@section('title', 'Document Registration')
@section('content')
    <div class="container">
        <div class="mt-5">
            <h2>Welcome, {{ Auth::user()->firstName }} {{ Auth::user()->lastName }}!</h2>
            <p>Upload your document below:</p>
            
            @if($errors->any())
                <div class="col-12">
                    @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{$error}}</div>
                    @endforeach
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger">{{session('error')}}</div>
            @endif

            @if(session()->has('success'))
                <div class="alert alert-success">{{session('success')}}</div>
            @endif
        </div>
        <form action="{{route('docuReg.post')}}" method="POST" enctype="multipart/form-data" class="ms-auto me-auto mt-5" style="width: 500px">
            @csrf
            <div class="mb-3">
                <label class="form-label">Document Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Document Type</label>
                <select class="form-control" name="documentType" required>
                    <option value="">Select Type</option>
                    <option value="Memo">Memo</option>
                    <option value="Report">Report</option>
                    <option value="Letter">Letter</option>
                    <option value="Proposal">Proposal</option>
                    <option value="Instructions">Instructions</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload File (PDF or Word)</label>
                <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Document</button>
            <a href="{{ route('logout') }}" class="btn btn-secondary">Logout</a>
        </form>
    </div>
@endsection