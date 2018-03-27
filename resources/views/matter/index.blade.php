@extends('layouts.app')

@section('script')
<script type="text/javascript">

// This needs to be run the first time and every time the matter list is updated via Ajax
function contentUpdated() {
	// Show/hide the data depending on the active radio button
	if ( $('#actor-status label.active input').val() == 1 ) {
		$('.display_actor, .display_status').hide();
		$('.display_status').show();
	} else {
		$('.display_status, .display_actor').hide();
		$('.display_actor').show();
	}
};

function refreshMatterList() {
	var url = '/matter?' + $(".btn-toolbar, #filter").find("input").filter(function(){return $(this).val().length > 0}).serialize(); // Filter out empty values
	$('#matter-list').load(url + ' #matter-list > tr', function() { // Refresh all the tr's in tbody#matter-list
		contentUpdated();
		window.history.pushState('', 'phpIP' , url);
	})
}

$(document).ready(function() {

	contentUpdated();

	$('.sortable').click(function() {
		$('#sort_id').val( $(this).data('sortkey') );
		$('#sort_dir').val( $(this).data('sortdir') );
		if ( $(this).data('sortdir') == 'asc' ) {
			$(this).data('sortdir', 'desc');
		} else {
			$(this).data('sortdir', 'asc');

		}
		refreshMatterList();
	});

	// Toggle the data to display
	$('#show-status').change(function() {
		$('.display_actor').hide();
		$('.display_status').show();
	});

	$('#show-actor').change(function() {
		$('.display_status').hide();
		$('.display_actor').show();
	});

	$('#show-all, #show-containers, #show-responsible').change(function(){
		refreshMatterList();
	});

  $('#export').click(function(e){
		var url = '/matter/export?' + $(".btn-toolbar, #filter").find("input").filter(function(){return $(this).val().length > 0}).serialize();
		e.preventDefault();  //stop the browser from following
    	window.location.href = url;
	});

	$('.filter-input').keyup(_.debounce(function(){
		if($(this).val().length != 0)
			$(this).css("background-color", "bisque");
		else
			$(this).css("background-color", "white");
		refreshMatterList();
	}, 500));
});
</script>
@stop

@section('content')
<div class="card" style="margin-bottom: 0px;">
	<div class="card-header">
    <form class="btn-toolbar" role="toolbar">
      <div class="btn-group btn-group-toggle mr-3" data-toggle="buttons" id="container-all">
        <label class="btn btn-info active">
          <input type="radio" id="show-all" name="Ctnr" value=""> Show All
				</label>
        <label class="btn btn-info">
          <input type="radio" id="show-containers" name="Ctnr" value="1"> Show Containers
				</label>
      </div>
      <div class="btn-group btn-group-toggle mr-3" data-toggle="buttons" id="actor-status">
        <label class="btn btn-info active">
          <input type="radio" id="show-actor" value="0"> Actor View
				</label>
        <label class="btn btn-info">
          <input type="radio" id="show-status" value="1"> Status View
				</label>
      </div>
      <div class="btn-group-toggle mr-3" id="mine-all" data-toggle="buttons">
        <label class="btn btn-info {{ @$_GET['responsible'] ? 'active' : '' }}">
          <input class="responsible-filter" type="checkbox" id="show-responsible" name="responsible" value="{{ Auth::user ()->login }}"> Show Mine
				</label>
      </div>
      <input type="hidden" id="sort_id" name="sort" value="{{ $matters->sort_id }}">
      <input type="hidden" id="sort_dir" name="dir" value="{{ $matters->sort_dir }}">
      <input type="hidden" id="display" name="display" value="{{ $matters->category_display }}">
      <div class="btn-group mr-3">
        <button id="export" type="button" class="btn btn-primary"> &DownArrowBar; Export</button>
			</div>
			<div class="button-group">
        <button id="clear-filters" type="button" class="btn btn-primary" onclick="$('#matter-list').load('/matter #matter-list > tr', function() {
					$('#filter').find('input').val('').css('background-color', '#fff');
					contentUpdated();
					$('#mine-all > label').removeClass('active');
					window.history.pushState('', 'phpIP' , '/matter');
				});">
				&circlearrowright; Clear filters</button>
      </div>
    </form>
	</div>
</div>
<table class="table table-striped table-hover table-sm">
  <thead>
    <tr>
      <th><a href="#" class="sortable" data-sortkey="caseref" data-sortdir="desc">Reference</a></th>
      <th>Cat.</th>
      <th><a href="#" class="sortable" data-sortkey="Status" data-sortdir="asc">Status</a></th>
      <th class="display_actor"><a href="#" class="sortable" data-sortkey="Client" data-sortdir="asc">Client</a></th>
      <th class="display_actor">Client Ref.</th>
      <th class="display_actor"><a href="#" class="sortable" data-sortkey="Agent" data-sortdir="asc">Agent</a></th>
      <th class="display_actor">Agent Ref.</th>
      <th class="display_actor">Title/Detail</th>
      <th class="display_actor"><a href="#" class="sortable" data-sortkey="Inventor1" data-sortdir="asc">Inventor</a></th>
      <th class="display_status"><a href="#" class="sortable" data-sortkey="Status_date" data-sortdir="asc">Date</a></th>
      <th class="display_status"><a href="#" class="sortable" data-sortkey="Filed" data-sortdir="asc">Filed</a></th>
      <th class="display_status">Number</th>
      <th class="display_status"><a href="#" class="sortable" data-sortkey="Published" data-sortdir="asc">Published</a></th>
      <th class="display_status">Number</th>
      <th class="display_status"><a href="#" class="sortable" data-sortkey="Granted" data-sortdir="asc">Granted</a></th>
      <th class="display_status">Number</th>
    </tr>
    <tr class="sticky-top" id="filter">
      <td><input class="filter-input form-control form-control-sm" name="Ref" placeholder="Ref" value="{{ old('Ref') }}"></td>
      <td><input class="filter-input form-control form-control-sm" size="3" name="Cat" placeholder="Cat" value="{{ old('Cat') }}"></td>
      <td><input class="filter-input form-control form-control-sm" name="Status" placeholder="Status" value="{{ old('Status') }}"></td>
      <td class="display_actor"><input class="filter-input form-control form-control-sm" name="Client" placeholder="Client" value="{{ old('Client') }}"></td>
      <td class="display_actor"><input class="filter-input form-control form-control-sm" size="8" name="ClRef" placeholder="Cl. Ref" value="{{ old('ClRef') }}"></td>
      <td class="display_actor"><input class="filter-input form-control form-control-sm" name="Agent" placeholder="Agent" value="{{ old('Agent') }}"></td>
      <td class="display_actor"><input class="filter-input form-control form-control-sm" size="16" name="AgtRef" placeholder="Agt. Ref" value="{{ old('AgtRef') }}"></td>
      <td class="display_actor"><input class="filter-input form-control form-control-sm" name="Title" placeholder="Title" value="{{ old('Title') }}"></td>
      <td class="display_actor"><input class="filter-input form-control form-control-sm" name="Inventor1" placeholder="Inventor" value="{{ old('Inventor1') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="Status_date" placeholder="Date" value="{{ old('Status_date') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="Filed" placeholder="Filed" value="{{ old('Filed') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="FilNo" placeholder="Number" value="{{ old('FilNo') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="Published" placeholder="Published" value="{{ old('Published') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="PubNo" placeholder="Number" value="{{ old('PubNo') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="Granted" placeholder="Granted" value="{{ old('Granted') }}"></td>
      <td class="display_status"><input class="filter-input form-control form-control-sm" name="GrtNo" placeholder="Number" value="{{ old('GrtNo') }}"></td>
    </tr>
	</thead>
  <tbody id="matter-list">
	@foreach ($matters as $matter)
	<?php // Format the publication number for searching on Espacenet
		$published = 0;
		if ( $matter->PubNo || $matter->GrtNo) {
			$published = 1;
			if ( $matter->origin == 'EP' )
				$CC = 'EP';
			else
				$CC = $matter->country;
			$removethese = [ "/^$matter->country/", '/ /', '/,/', '/-/', '/\//' ];
			$pubno = preg_replace ( $removethese, '', $matter->PubNo );
			if ( $CC == 'US' ) {
				if ( $matter->GrtNo )
					$pubno = preg_replace ( $removethese, '', $matter->GrtNo );
				else
					$pubno = substr ( $pubno, 0, 4 ) . substr ( $pubno, - 6 );
			}
		}
	?>
		@if ( $matter->container_id )
		<tr>
		@else
		<tr class="table-info">
		@endif
			<td {!! $matter->dead ? 'style="text-decoration: line-through"' : '' !!}><a href="/matter/{{ $matter->id }}" target="_blank">{{ $matter->Ref }}</a></td>
			<td>{{ $matter->Cat }}</td>
			<td>
			@if ( $published )
				<a href="http://worldwide.espacenet.com/publicationDetails/biblio?DB=EPODOC&CC={{ $CC }}&NR={{ $pubno }}" target="_blank" title="Open in Espacenet">{{ $matter->Status }}</a>
			@else
				{{ $matter->Status }}
			@endif
			</td>
			<td class="display_actor">{{ $matter->Client }}</td>
			<td class="display_actor">{{ $matter->ClRef }}</td>
			<td class="display_actor">{{ $matter->Agent }}</td>
			<td class="display_actor">{{ $matter->AgtRef }}</td>
			<td class="display_actor small">{{ $matter->Title }}</td>
			<td class="display_actor">{{ $matter->Inventor1 }}</td>
			<td class="display_status">{{ $matter->Status_date }}</td>
			<td class="display_status">{{ $matter->Filed }}</td>
			<td class="display_status">{{ $matter->FilNo }}</td>
			<td class="display_status">{{ $matter->Published }}</td>
			<td class="display_status">{{ $matter->PubNo }}</td>
			<td class="display_status">{{ $matter->Granted }}</td>
			<td class="display_status">{{ $matter->GrtNo }}</td>
		</tr>
		@endforeach
		<tr>
			<td colspan="9">
				<nav class="fixed-bottom">
					<ul class="pagination justify-content-center">
						<li class="page-item">
							<a class="page-link" href="#" onclick="$('#matter-list').load('{!! $matters->previousPageUrl() !!}' + ' #matter-list > tr', function() {
								contentUpdated();
								window.history.pushState('', 'phpIP' , '{!! $matters->previousPageUrl() !!}');
							});">&laquo;</a>
						</li>
						<li class="page-item">
							<a class="page-link" href="#" onclick="$('#matter-list').load('{!! $matters->nextPageUrl() !!}' + ' #matter-list > tr', function() {
								contentUpdated();
								window.history.pushState('', 'phpIP' , '{!! $matters->nextPageUrl() !!}');
							});">&raquo;</a>
						</li>
					</ul>
				</nav>
			</td>
		</tr>
	</tbody>
</table>

@stop
