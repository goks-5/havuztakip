<?php 
	include_once 'gums_db.php';
	include 'connect_db.php';
	$result = mysqli_query($conn,"SELECT * FROM gums");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Duyusal Analiz</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="styles.css">

</head>
<body>
	<br>
	<br>
	<h3 class="text-center text-success" id="message"><?php echo  $success; ?></h3>
    <div class="container">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
						<h2>Manage <b>Gum</b></h2>
					</div>
					<div class="col-sm-6">
					    <a href="#deleteGumsModal" class="btn btn-danger"  data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Delete">&#xE872;</i><span>Delete</span></a>
						<a href="#editGumsModal"   class="btn btn-info"    data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Edit">  &#xE254;</i><span>Edit</span></a>
						<a href="#addGumsModal"    class="btn btn-success" data-toggle="modal"><i class="material-icons" data-toggle="tooltip" title="Add">   &#xE147;</i><span>Add</span></a>											
					</div>
                </div>
			</div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
						<th>
							<span class="custom-checkbox">
								<input type="checkbox" id="selectAll">
								<label for="selectAll"></label>
							</span>
						</th>
                        <th>Gum ID</th>
                        <th>Data 1</th>
			            <th>Data 2</th>
                        <th>Comments</th>
                        <th>Created</th>
                        <th>Updated</th>
                    </tr>
                </thead>
                <tbody>
				<?php
                while($row = mysqli_fetch_array($result))
                {
                ?>
					<tr>
						<td>
							<span class="custom-checkbox">
								<input type="checkbox" id="checkbox1" name="options[]" value="1">
								<label for="checkbox1"></label>
							</span>
						</td>
						<td><?php echo $row["gumID"]; ?></td>
						<td><?php echo $row["data1"]; ?></td>
						<td><?php echo $row["data2"]; ?></td>
						<td><?php echo $row["comment"]; ?></td>
						<td><?php echo $row["created_at"]; ?></td>
						<td><?php echo $row["updated_at"]; ?></td>
					</tr>
                <?php 
                    } 
                ?>
				<?php
				
					// close connection database
					mysqli_close($conn);
                ?>
                </tbody>
            </table>
			<div class="clearfix">
                <div class="hint-text">Showing <b>5</b> out of <b>100</b> entries</div>
                <ul class="pagination">
                    <li class="page-item disabled"><a href="#">Previous</a></li>
                    <li class="page-item active"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                    <li class="page-item"><a href="#" class="page-link">3</a></li>
                    <li class="page-item"><a href="#" class="page-link">4</a></li>
                    <li class="page-item"><a href="#" class="page-link">5</a></li>
                    <li class="page-item"><a href="#" class="page-link">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
	<!-- Add Modal HTML -->
	<div id="addGumsModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form method="post" action="gum.php">
					<div class="modal-header">						
						<h4 class="modal-title">Add Gum</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Gum ID</label>
							<input type="text" class="form-control" name="gumID" placeholder="Enter Gum ID" required>
						</div>
						<div class="form-group">
							<label>Data 1</label>
							<input type="text" class="form-control" name="data1" placeholder="Enter Data 1" required>
						</div>
						<div class="form-group">
							<label>Data 2</label>
							<input type="text" class="form-control" name="data2" placeholder="Enter Data 2" required>
						</div>
						<div class="form-group">
							<label>Comment</label>
							<textarea class="form-control" name="comment" placeholder="Enter Comment" required></textarea>
						</div>					
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-success" name="add" value="Add">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Edit Modal HTML -->
	<div id="editGumsModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
					<div class="modal-header">						
						<h4 class="modal-title">Edit Gum</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<div class="form-group">
							<label>Gum ID</label>
							<input type="text" class="form-control" required>
						</div>
						<div class="form-group">
							<label>Data 1</label>
							<input type="text" class="form-control" required>
						</div>
						<div class="form-group">
							<label>Data 2</label>
							<input type="text" class="form-control" required>
						</div>
						<div class="form-group">
							<label>Comment</label>
							<textarea class="form-control" required></textarea>
						</div>					
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-info" value="Save">
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Delete Modal HTML -->
	<div id="deleteGumsModal" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<form>
					<div class="modal-header">						
						<h4 class="modal-title">Delete Gum</h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">					
						<p>Are you sure you want to delete these records?</p>
						<p class="text-warning"><small>This action cannot be undone.</small></p>
					</div>
					<div class="modal-footer">
						<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
						<input type="submit" class="btn btn-danger" value="Delete">
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
        $(document).ready(function()
        {
            setTimeout(function()
            {
                $('#message').hide();
            },3000);
        });
    </script>

<script src="javascript.js"></script>

</body>
</html>                                		                            