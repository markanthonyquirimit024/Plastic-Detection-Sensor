@include('layout.base')
<title>User Management</title>

<style>
#usertable {
    background: #f5f5f510;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    margin-top: 200px;
}
</style>


<div class="main-content" id="main-content">
  <div class="container-fluid">
    <div class="table-responsive" id="usertable">
    <div style="text-align: end;"><p><a href="{{route('admin.create-user')}}" class="btn btn-primary">Create Analyst</a></p></div>
      <table class="table table-bordered table-light table-striped text-center">
        <thead class="table-info">
          <tr>
            <th scope="col">ID</th>
            <th scope="col">NAME</th>
            <th scope="col">EMAIL</th>
            <th scope="col">ROLE</th>
            <th scope="col">ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Admin</td>
            <td>admin@phinmaed.com</td>
            <td>Admin</td>
            <td></td>
          </tr>
          <tr>
            <td>2</td>
            <td>Admin</td>
            <td>admin@phinmaed.com</td>
            <td>Admin</td>
            <td></td>
          </tr>
          <tr>
            <td>3</td>
            <td>Admin</td>
            <td>admin@phinmaed.com</td>
            <td>Admin</td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
