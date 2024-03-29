@extends('layouts.app')

@section('content')
@php 
$options = [0=>'No', 1=>'Yes'];
@endphp
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3>{{ $opportunity->contact_name }} at {{ $opportunity->company_name }}</h3>
        </div>
        <ul class="list-group">
            <li class="list-group-item"><strong>Is decision maker: </strong></strong>{{ $options[$opportunity->decision_maker] }}</li>
            <li class="list-group-item"><strong>Phone: </strong>{{ $opportunity->contact_phone }}</li>
            <li class="list-group-item"><strong>Email: </strong>{{ $opportunity->contact_email }}</li>
            <li class="list-group-item"><strong>Company State: </strong>{{ $opportunity->company_state }}</li>
            <li class="list-group-item"><strong>Employees: </strong>{{ $opportunity->employees_number }}</li>
        </ul>
        <div class="panel-footer">
            &nbsp;
            <a href="{{ url()->previous() }}" class="btn btn-default">Go back</a>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3><strong>Client ID: </strong>{{ $opportunity->client_id }}</h3>
        </div>
        <ul class="list-group">
            <li class="list-group-item"><strong>Status: </strong>{{ $opportunity->status_options[$opportunity->status] }} <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#status_modal">Update Status</button></li>
            @php $types = [null=>"N/A", 1=>"Cold Source", 2=>"Existing Source", 3=>"Account outreach"] @endphp
            <li class="list-group-item"><strong>Lead Type: </strong>{{ $types[$opportunity->lead_type] }}</li>
            <li class="list-group-item"><strong>VitalFew Agent: </strong>{{ $opportunity->vf_agent }}</li>
            <li class="list-group-item"><strong>Creation Date: </strong>{{ $opportunity->creation_date }}</li>
            <li class="list-group-item"><strong>Invitation Date: </strong>{{ $opportunity->event_date }} <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#invite_modal">Change Date</button></li>
            <li class="list-group-item"><strong>{{ $opportunity->client->name }} Agent: </strong>{{ $opportunity->client_agent }} <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#agent_modal">Change Agent</button></li>
        </ul>

        <div class="panel-body">
            <div class="alert alert-warning alert-dismissible" role="alert" id="note_warning" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> Notes can not be empty.
            </div>
            <div class="alert alert-danger alert-dismissible" role="alert" id="note_danger" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Error!</strong> An error occurred. Please, try again. If the error persists, please communicate with site's admin.
            </div>
            <div class="alert alert-success alert-dismissible" role="alert" id="note_success" style="display:none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Warning!</strong> Notes were saved successfully.
            </div>
            <div class="form-group">
                <label>Notes:</label>
                <textarea name="comment" class="form-control" id="comments" cols="30" rows="10">{{ $opportunity->comment }}</textarea>
            </div>
        </div>
        <div class="panel-footer">
            <button type="button" class="btn btn-primary" id="save_notes">Save notes</button>
        </div>
    </div>

</div>
<!-- Status modal -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="status_modal">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
    <form action="{{ route('opportunity.status.store', [$opportunity->id]) }}" method="POST">
        {{ csrf_field() }}
        <div class="modal-body">
            <select name="status" id="status_id" class="form-control">
                @foreach($status_options as $value => $name)
                    <option value="{{ $value }}" @if($value == $status_id) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Update</button>
        </div>
    </form>
    </div>
  </div>
</div>

<!-- Change agent modal -->
<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="Change Agent" id="agent_modal">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
    <form action="{{ route('opportunity.agent.store', [$opportunity->id]) }}" method="POST">
        {{ csrf_field() }}
        <div class="modal-body">
            <select name="status" id="agent_id" class="form-control">
                @foreach($status_options as $value => $name)
                    <option value="{{ $value }}" @if($value == $status_id) selected @endif>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Update</button>
        </div>
    </form>
    </div>
  </div>
</div>

<!-- Small modal -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="Modal invite" id="invite_modal">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
    <form method="post" action="{{ route('opportunity.invite.update', [ $opportunity->id ]) }}">
        {{ csrf_field() }}
        <div class="modal-body">
            <div class="row">
                <div class='col-sm-6'>
                    <div class="form-group">
                        <label for="">Gusto Agent</label>
                        <select name="agent_id" class="form-control">
                            <option value="">Select a Gusto agent</option>
                        @foreach($opportunity->agents_options as $index => $value)
                            <option value="{{ $index }}"@if($index == $opportunity->agent_id) selected @endif>{{ $value['name'] }}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class='col-sm-6'>
                    <div class="form-group">
                        <label for="">Timezone</label>
                        <select name="timezone" id="timezone" class="form-control select2" required>
                            <option value="">Select a timezone</option>
                        @foreach($opportunity->tzlist_options as $index => $value)
                            <option value="{{ $index }}" @if($index == "America/New_York") selected @endif>{{ $value }}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-sm-6'>
                    <div class="form-group">
                        <label for="">Date and Time</label>
                        <div class='input-group date' id='update_date'>
                            <input type='text' class="form-control" name="date" @if($opportunity->date) value="{{ \Carbon\Carbon::parse($opportunity->date, $opportunity->timezone)->format('m/d/Y h:i A') }}" @endif />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="form-group">
                <div class="checkbox">
                <label>
                    <input type="checkbox" name="send_calendar_invite" id="#send_calendar_invite" value="1"> Send google calendar invite
                </label>
                </div>
                <button class="btn btn-primary">Update</button>
            </div>
        </div>
        </form>
    </div>
  </div>
</div>
@endsection

@section('bottom_script')
<script>
$(document).ready(function () {
    $("#save_notes").on('click', function(){
        var comments = $("#comments").val();
        if(comments == ''){
            $("#note_danger, #note_success").hide();
            $("#note_warning").show();
            return;
        }
        $("#save_notes").addClass('disabled');
        $.ajax({
            type: "POST",
            url: "{{ route('opportunity.comments.store', [$opportunity->id]) }}",
            data: {
                'comments': comments
            },
        })
        .done(function() {
            $("#note_warning, #note_danger").hide();
            $("#note_success").show();
            $("#save_notes").removeClass('disabled');
        })
        .fail(function() {
            $("#note_warning, #note_success").hide();
            $("#note_danger").show();
        });
    });
    $('#update_date').datetimepicker({
        daysOfWeekDisabled: [0, 6],
        //inline: true,
        sideBySide: true,
        stepping: 15,
        //minDate: moment(),
        useCurrent: false,
    });
});
</script>
@endsection
