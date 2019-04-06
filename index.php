<?php
//https://cf-hls-opus-media.sndcdn.com/playlist/6a4yq7hqSkbp.64.opus/playlist.m3u8?Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiKjovL2NmLWhscy1vcHVzLW1lZGlhLnNuZGNkbi5jb20vcGxheWxpc3QvNmE0eXE3aHFTa2JwLjY0Lm9wdXMvcGxheWxpc3QubTN1OCIsIkNvbmRpdGlvbiI6eyJEYXRlTGVzc1RoYW4iOnsiQVdTOkVwb2NoVGltZSI6MTU0NDQ2NDY0OH19fV19&Signature=dJw27Sh63zsceJHyrS7XFgrE9fmIfKUpJ6pyrE7aFQGofcOcR2ISoEByMCp5hedwqqLnYcFR6VRE20ZrVGeeXU-VsoZKWpUFQawJcIyWgSbC9BA8YOpXZbsBEDrgIy44Lx3YUzBbDMTJf-I0mqgVxEX0aV7XR46bXeaODVTL1gCzUG-nkCr6B8ZRcV0i~asxWM5pgpRioTkeg5FsJCQNOQIpK418CRUWvkH0~Etp1SYiMHfbnz7We1jA76ze4fdxy40bt9ve6n-llRW2rXbry-DmmI-bhECAFKMTmyOn3KjNyGF0QQXGzRgoKApSzSZqfna~KQoJxDkI-Th4DpB0mQ__&Key-Pair-Id=APKAJAGZ7VMH2PFPW6UQ
//
include('header.php');

?>

	<style> 
		.collapsed_description {
			max-height:10em;
			overflow:hidden;
		}
		.glyphicon-refresh-animate {
			-animation: spin 1.2s infinite linear;
			-webkit-animation: spin2 1.2s infinite linear;
		}

		@-webkit-keyframes spin2 {
			from { -webkit-transform: rotate(0deg);}
			to { -webkit-transform: rotate(360deg);}
		}

		@keyframes spin {
			from { transform: scale(1) rotate(0deg);}
			to { transform: scale(1) rotate(360deg);}
		}
	</style>

	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="form-group">
					<p>Youtube-dl web access</P>
					<p>Samples</p>
					<ul>
						<li><a onclick="$('#url-target').val('https://www.youtube.com/watch?v=5kC4_iEXhGc');">Pink Guy - Kill yourself</a></li>
						<li><a onclick="$('#url-target').val('https://www.youtube.com/watch?v=QOmin4vmMts');">The Last Ninja Music</a></li>
						<li><a onclick="$('#url-target').val('https://www.youtube.com/watch?v=9C_HReR_McQ');">Don't hug me i'm scared </a></li>
					</ul>
					<!-- <ul>
					<li>https://codepen.io/nksimmons/pen/NqGdNo</li>
					<li>https://stackoverflow.com/questions/18545292/how-to-have-a-progress-bar-in-jquery-get</li>
					</ul> -->
					<label for="url-target">Link to Video</label>
					<input type="text" class="form-control" id="url-target" placeholder="URL To Video">
				</div>
				<div class="form-group">
					<button id="querycat" class="btn btn-default">Get Direct Link</button>
					<button id="clearcat" class="btn btn-default">Clear</button>
				</div>
			</div>
		</div>
	</div>

	<hr>
	
	<!-- Slurping -->
	<div class="slurping hidden"><div class="row"><div class="col-xs-12">
		<div class="row"><div class="col-xs-12">
			<h1 style="text-align: center"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>&nbsp;Slurping Media</h1>
		</div></div>
		<div class="row"><div class="col-xs-12">
			<p class="slurp_target" style="text-align: center"></p>
		</div></div>
	</div></div></div>

	<!-- Template Elements which are cloned and inserted at different places via JS -->
	<!-- A row element for the format table -->
	<table class="hidden">
	<tr class="video_format_row hidden">
		<td    class="video_format_entry_id hidden">ID</td>
		<td    class="video_format_entry_ext">Extension</td>
		<td    class="video_format_entry_format">Audio</td>
		<td    class="text-right video_format_entry_filesize">Size</td>
		<td><a download class="video_format_entry_url"><span class="glyphicon glyphicon-download-alt"></span></a></td>
	</tr>
	</table>

	<div id="query_results"></div>

	<nav class="navbar navbar-fixed-bottom navbar-inverse hidden" style="margin-bottom:0";>
		<div class="container-fluid">
		<div class="navbar-header gray-lighter">
			Downloads
		</div>
		<div class="collapse navbar-collapse">			
			<div class="progress" style=";margin-bottom:0">
				<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
				<span class="sr-only">45% Complete</span>
				</div>
			</div>		
		</div>
		</div>
	</nav>

	<!-- The query result -->
	<div class="container-fluid video_container hidden" style="padding-bottom:1em;">
		<div class="row">
			<!-- Thumbnail -->
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-12">
					&nbsp;
					</div>
				</div>
				<img src="" class="img-responsive video_thumbnail">
			</div>
			<div class="col-md-8">
				<!-- Title -->
				<div class="row">
					<div class="col-md-12">
						<h3 style="overflow-x: auto;display:inline text-overflow:ellipsis; display:block;white-space: nowrap" class="video_title">Title</h3>
						<span class="video_duration" style="white-space:nowrap;">Duration</span>
					</div>
				</div>
				<!-- Description -->
				<div class="row">
					<div class="col-md-12">
						<p class="video_description collapsed_description">Description</p>
						<strong style="cursor: pointer; padding-bottom:1em;" class="text-uppercase showmore_description">Show More</strong>
						<strong style="cursor: pointer; padding-bottom:1em;" class="text-uppercase showless_description hidden">Show Less</strong>
					</div>
				</div>
				<!-- Download button area -->
				<div class="row">
					<div class="col-xs-12">
						<button class="video_download btn-success btn btn-xs">Default Video</button>
						<button class="audio_download btn-success btn btn-xs">Smallest Audio</button>
						<button class="show_formats_collapser btn-info btn btn-xs" data-toggle="collapse" type="button">All Formats <span class="caret"></span></button>
					</div>
				</div>
				<!-- Foldable formats table -->
				<div class="collapse hidden_area bg-info well well-sm">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-hover table-condensed">
								<thead>
									<tr>
										<th style="white-space:nowrap" class="hidden">ID</th>
										<th style="white-space:nowrap">Ext</th>
										<th style="width:100%">Format</th>
										<th style="white-space:nowrap">File Size</th>
										<th style="white-space:nowrap"></th>
									</tr>
								</thead>
								<tbody class="video_format_table">
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<!--  -->
			</div>
		</div>
	</div>

<?php
include('footer.php');
?>
