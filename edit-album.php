<div class="col-12 col-md-6 mb-5">
						<h2 class="text-center">Album Information</h2>
						<hr class="col-3 col-sm-3 col-md-2 col-lg-1 mx-auto bg-light">
						<h5 class="text-center"><?php echo $creator['first'] .' '. $creator['last'];?> | <?php echo date("F jS, Y", strtotime($row['uploadDate']));?></h5>
						<form class="col-10 mx-auto row" action="" method="post">
							<div class="form-group col-12">
								<label for="title">Title</label>
								<h4 class="invalid-feedback d-block"><?php echo $titleErr;?></h4>
								<input class="form-control" type="text" name="title" value="<?php echo $row['title'];?>" required>
							</div>
							<div class="form-group col-12 col-sm-6">
								<label for="location">Location</label>
								<h4 class="invalid-feedback d-block"><?php echo $locationErr;?></h4>
								<input class="form-control" type="text" name="location" value="<?php echo $row['location'];?>" required>
							</div>
							<div class="form-group col-12 col-sm-6">
								<label for="eventDate">Event Date</label>
								<h4 class="invalid-feedback d-block"><?php echo $eventDateErr;?></h4>	
								<input class="form-control" type="date" name="eventDate" value="<?php echo $row['eventDate'];?>" required>
							</div>
							<div class="form-group col-12">
								<label for="participants">Participants</label>
								<h4 class="invalid-feedback d-block"><?php echo $participantsErr;?></h4>
								<input class="form-control" type="text" name="participants" value="<?php echo $row['participants'];?>" required>
							</div>
							<div class="form-group col-12">
								<label for="description">Description</label>
								<h4 class="invalid-feedback d-block"><?php echo $descriptionErr;?></h4>
								<textarea class="form-control" name="description" required><?php echo $row['description'];?></textarea>
							</div>
							<div class="form-group col-12">
								<button type="reset" class="btn btn-danger">Reset</button>
								<button type="submit" value="update" name="submit" class="btn btn-info float-right">Update</button>
							</div>
						</form>
					</div>