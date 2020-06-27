	</div>
	<script src="/public/plyr/plyr.polyfilled.js"></script>
	<link rel="stylesheet" href="/public/plyr/plyr.css" />
	
	<div data-role="content" data-theme="a">
		<h3>Partie 1</h3>
		
		<script>
		    const player = new Plyr('#player');
		</script>
		<div style="width:100%;height:100%;background:#000">
		<div class="plyr">
			<video src="/public/videos/HankTV_PART1.mp4" id="player" controls controlsList="nodownload" playsinline poster="/public/plyr/cover.png" data-plyr-config='{ "title": "Partie 1" }' />
		</div>
		</div>
		<h3>Partie 2</h3>

		<div style="width:100%;height:100%;background:#000">
		<div class="plyr">
			<video src="/public/videos/HankTV_PART2.mp4" id="player" controls controlsList="nodownload" playsinline poster="/public/plyr/cover.png" data-plyr-config='{ "title": "Partie 1" }' />
		</div>
		</div>
		
		<h3>Partie 3</h3>
		
		A venir...
		
	</div><!-- /content -->
</div><!-- /page -->

	<script type="text/javascript">
		$(document).ready(function(){
		   $('#player').bind('contextmenu',function() { return false; });
		});
</script>