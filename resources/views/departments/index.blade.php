@extends('layouts.admin')
@section('content')
<section class="content">
    <div class="card card-primary">
        <div class="card-header">
        <h3 class="card-title">Departments</h3>
        </div>
        
        
        <form>
        <div class="card-body">
        <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
        </div>
       
        <div class="card-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="submit" class="btn btn-danger float-right">Cancel</button>
        
        </div>
        </form>
        </div>
</section>

@endsection