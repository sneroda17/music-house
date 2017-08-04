@extends('index')
@section('meta-content')
	<meta name="twitter:card" value="photo"/>
	<meta name="twitter:site" value="{{ '@'.$settings->twitter_page_id }}"/>
	<meta name="twitter:image" value="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:site_name" content="{{ $settings->website_name }}"/>
	<meta property="og:url" content="{{ URL::to('artist') }}"/>
	<meta property="og:title" content="{{ $settings->website_name }} - {{ Lang::get('words.all-artists') }}"/>
	<meta property="og:image" content="{{ URL::asset('assets/images/logo.png') }}"/>
	<meta property="og:description" content="{{ $settings->website_name }} - {{ Lang::get('words.all-artists') }}"/>
	<title>{{ $settings->website_name }} - {{ Lang::get('words.all-artists') }}</title>
@stop

@section('content')
<?php
	$aa_counts = DB::table('albums')->whereIn('artist_id', $artists->lists('id'))->select(DB::raw('artist_id, count(id) AS count'))->groupBy('artist_id')->get();
	$aa_array = array();
	foreach($aa_counts as $aa_count) {
		$aa_array[$aa_count->artist_id] = $aa_count->count;
	}
?>
<div class="row">
	<div style="padding:15px">
		<div class="hidden-xs text-center" style="width:728px;margin:15px auto 0">
			<iframe src="{{ URL::to('ads/banner') }}" width='728' height="90" frameborder='0' border='0' marginwidth='0' marginheight='0' scrolling='no'></iframe>
		</div>
		<h2 style="margin-top:0" class="text-muted">{{ Lang::get('words.artists') }}</h2>
		<div class="scroll-container">
			@forelse($artists as $index => $artist)
				<?php $album_count = isset($aa_array[$artist->id]) ? $aa_array[$artist->id] : 0; ?>
				<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4 scroll-items" style="padding:0; margin-bottom:15px">
					<div class="album">
			    		<div class="bg-transparent">
			    			<div class="transparent-img" style="filter:blur(0);background-image:url('@if($artist->image) {{ URL::to('uploads/artists/'.$artist->image.'/'.$artist->slug.'.jpg') }} @else {{ URL::asset('assets/images/default.jpg') }} @endif')"></div>
				    		<a title="{{ Lang::get('words.browse-albums', array('title' => $artist->name)) }}" class="pjax" href="{{ URL::to('artist/'.$artist->slug) }}" style="display:block;position:relative; padding:5px">
				        		<img src="@if($artist->image) {{ URL::to('uploads/artists/'.$artist->image.'/'.$artist->slug.'.jpg') }} @else {{ URL::asset('assets/images/default.jpg') }} @endif" alt="{{ $artist->name }}" class="img-circle">
				        	</a>
			        	</div>
			        	<div class="album-info">
			        		<span class="album-title">{{ $artist->name }}</span>
			        		<span class="album-artist">{{ Lang::get('words.albums') }} ({{ $album_count }})</span>
			        		<div class="clearfix"></div>
			        	</div>
			       	</div>
			    </div>
			@empty
				<div class="alert alert-danger"><i class="fa fa-meh-o"></i> Sorry, No Artists found</div>
			@endforelse
			<div class="clearfix"></div>
		</div>
		{{ $artists->links('pagination::simple') }}
	</div>
</div>
@stop