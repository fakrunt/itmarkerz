@extends('app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading"><h3>Questions & Replies</h3></div>
				<div class="panel-body">
					@if (!Auth::guest())<div class="row"><a href="{{URL::to('/')}}/question/add" class="pull-right"><h4>Add Question</h4></a></div>@endif
					@if (count($errors) > 0)
						<div class="alert alert-danger">
							<strong>Whoops!</strong> There were some problems with your input.<br><br>
							<ul>
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					@if(isset($questions) && count($questions) > 0)
						<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
							@foreach ($questions as $key => $question)
							<div class="panel panel-default">
				    			<div class="panel-heading" role="tab" id="headingOne">
				      				<h4 class="panel-title">
				        			<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$question->id}}" aria-expanded="true" aria-controls="collapse{{$question->id}}" quest-id="{{$question->id}}" class="question">
				          			{{$key+1}}. {{isset($question->question)?$question->question:''}} @if (!Auth::guest())<a class="reply-link"><span class="pull-right answer" ques-id="{{$question->id}}" question="{{$question->question}}">Add reply</span></a>@endif
				        			</a>
				      				</h4>
				    			</div>
				    			@if(($key+1) == 1)
				    			<div id="collapse{{$question->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
				      				<div class="panel-body" id="demo{{$question->id}}">
				        				@if(isset($firstAnswers) && count($firstAnswers) > 0)
				        					<h4>Replies</h4>
				        					@foreach ($firstAnswers as $key1 => $firstAnswer)
				        						<p>{{$key1+1}}. {{$firstAnswer->answer}} <span class="pull-right">Reply by: <a>{{$firstAnswer->name}}</a></span></p>
				        					@endforeach
				        				@endif
				      				</div>
				    			</div>
				    			@else
				    				<div id="collapse{{$question->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
					      				<div class="panel-body" id="demo{{$question->id}}">
					        				
					      				</div>
					    			</div>
				    			@endif
							</div>
							@endforeach
						</div>
					@else
						<p>No Questions added yet.
						@if (Auth::guest()) 
							Please <a href="{{URL::to('/')}}/auth/login">login</a> to add
						@endif
						</p>
					@endif
					<!-- Modal -->
					<div class="modal fade" id="myModal" role="dialog">
					    <div class="modal-dialog">
					      <!-- Modal content-->
					      <div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Add Reply</h4>
					        </div>
					        <div class="modal-body">
					          
					        </div>
					        <div class="modal-footer">
					          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					        </div>
					      </div>
					      
					    </div>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://code.jquery.com/jquery-git2.min.js"></script>
<script type="text/javascript">
	//Fetch answers with respect to question id
	$( ".question" ).on( "click", function( event ) {
		var questionId = $(this).attr("quest-id");
		$.ajax({
			url: '{{URL::to('/')}}/question/getAnswer',
		   	type: 'GET',
		   	data: {
		    	format: 'json',
		      	questionid: questionId
		   	},
		   	error: function() {
		    	$("#demo"+questionId).html(data.msg);
		   	},
			success: function(data) {
				if(data.status){
		    		$("#demo"+questionId).html(data.data);
				}else{
					$("#demo"+questionId).html(data.msg);
				}
		   	}, 
		});
	});

	//create modal view corresponding question clicked
	$( ".answer" ).on( "click", function( event ) {
		$("#myModal").modal("hide")
		var question = $(this).attr("question");
		var questionId = $(this).attr("ques-id");
		var html = '';
		html += '<h3>'+question+'</h3>';
		html += '<form class="form-horizontal"><input type="hidden" name="question_id" id="question_id" value="'+questionId+'">';
		html += '<div class="form-group"><label class="col-md-4 control-label">Answer</label>';
		html += '<div class="col-md-6"><textarea class="form-control" name="answer" id="answer"></textarea></div></div>';
		html += '<div class="form-group"><div class="col-md-6 col-md-offset-4"><button type="submit" class="btn btn-primary" style="margin-right: 15px;" onclick="return submitReply()">Add</button></div></div>';
		html += '</form>';
		$('.modal-body').html(html);
		$("#myModal").modal();
	});

	//Post answer with respect to question id
	function submitReply(){
		var answer = $("#answer").val();
		var questionId = $("#question_id").val();
		if(answer == ''){
			alert("answer is required.");
		}else{
			$.ajax({
			   	url: '{{URL::to('/')}}/question/addAnswer',
			   	type: 'POST',
			   	data: {
			    	format: 'json',
			      	questionid: questionId,
			      	answer: answer
			   	},
			   	error: function() {
			    	var html = '<div class="alert alert-error"><p>Reply posting failed.</p></div>';
			   		$(".modal-body" ).prepend(html);
			   	},
				success: function(data) {
					if(data.status){
						var html = '<div class="alert alert-success"><p>Reply posted successfully.</p></div>';
						$(".modal-body" ).prepend(html);
						$.ajax({
							url: '{{URL::to('/')}}/question/getAnswer',
						   	type: 'GET',
						   	data: {
						    	format: 'json',
						      	questionid: questionId
						   	},
						   	error: function() {
						    	$("#demo"+questionId).html(data.msg);
						   	},
							success: function(data) {
								if(data.status){
						    		$("#demo"+questionId).html(data.data);
								}else{
									$("#demo"+questionId).html(data.msg);
								}
						   	}, 
						});
			   		}else{
			   			var html = '<div class="alert alert-error"><p>Reply posting failed.</p></div>';
			   			$(".modal-body" ).prepend(html);
			   		}
			   }
			   
			});
		}
		return false;
	}
	
</script>
@endsection

