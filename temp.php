<div id="mediaCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
							<ol class="carousel-indicators">
								<li data-target="#mediaCarousel" data-slide-to="0" class="active"></li>
								<?php
									for($i=1; $i < $resMedia->num_rows; $i++){
										echo "<li data-target='#mediaCarousel' data-slide-to=".$i."></li>";
									}
								?>
							</ol>
							<div class="carousel-inner">
								<?php
									for($i=0; $i < $resMedia->num_rows; $i++){
										$currentMedia = $resMedia->fetch_assoc()["name"];
										if (preg_match("/video/",mime_content_type("./media/" . $currentMedia)) == 1){
											// Video Type
											echo '<div class="carousel-item">';
											echo '<video type="video/mp4" controls class="d-block mx-auto" src="/media/'.$currentMedia.'">';
											echo '</div>';
										}else{
											// Image Type
											echo '<div class="carousel-item">';
											echo '<img class="d-block mx-auto" src="/media/'.$currentMedia.'">';
											echo '</div>';
										}
									}
								?>							
							</div>
							<a class="carousel-control-prev" href="#mediaCarousel" role="button" data-slide="prev">
								<span class="carousel-control-prev-icon" aria-hidden="true"></span>
								<span class="sr-only">Previous</span>
							</a>
							<a class="carousel-control-next" href="#mediaCarousel" role="button" data-slide="next">
								<span class="carousel-control-next-icon" aria-hidden="true"></span>
								<span class="sr-only">Next</span>
							</a>
						</div>