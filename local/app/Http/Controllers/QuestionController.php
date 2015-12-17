<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Question;
use App\Answer;
use Auth;
class QuestionController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Question Controller
	|--------------------------------------------------------------------------
	|
	*/
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth',['except' => ['index','getAnswer']]);
	}

	/**
	 * Show the questions list to the user.
	 *
	 * @return Response
	*/
	public function index()
	{
		try{
			$data['questions'] = Question::all();
			if(isset($data['questions']) && count($data['questions']) > 0){
				//get Answers of first question with added user name
				$data['firstAnswers'] = Answer::leftJoin('users', function($join) {
			      $join->on('answers.user_id', '=', 'users.id');
			    })
			    ->where("question_id", "=", $data['questions'][0]->id)
			    ->get([
			        'answers.id',
			        'answers.answer',
			        'answers.question_id',
			        'users.id',
			        'users.name'
			    ]);
			}
			return view('question.view', $data);
		}catch(Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
	}

	/**
	 * Add the questions by user.
	 *
	 * @return Response
	*/
	public function add(Request $request)
	{
		try{
			if($request->input('question') !=''){
				$validator = Validator::make($request->all(), [
                    'question' => 'required|max:250'
                ]);
                if($validator->fails())
                {
                    return redirect()->back()->withInput()->withErrors($validator->errors());
                }else{
                	
                	$data = array(
                		"question" => $request->input('question'),
                		"user_id"  => Auth::user()->id
                	);
                	$questionAdd = Question::create($data);
                	if($questionAdd){
                		return redirect('/questions')->with('success', 'Question created successfully.');
                	}else{
                		return redirect()->back()->withInput()->withErrors("Cannot add now,plese try after some time");
                	}
                }
			}else{
            	return view('question.add');
			}
        }catch(Exception $e){
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
		//return view('home');
	}

	/**
	 * Get all the replies correponding to question's id by an user.
	 *	It's ajax function
	 * @return Response
	*/
	public function getAnswer(Request $request){
		$statusCode = 200;
        $response = [
            'status'  => false, 
            'msg' => ""
        ];
		if($request->input('questionid') != ''){
			//$answers = Answer::where("question_id", "=", $request->input('questionid'))->get();
			$answers = Answer::leftJoin('users', function($join) {
		      $join->on('answers.user_id', '=', 'users.id');
		    })
		    ->where("question_id", "=", $request->input('questionid'))
		    ->get([
		        'answers.id',
		        'answers.answer',
		        'answers.question_id',
		        'users.id',
		        'users.name'
		    ]);

			if(isset($answers) && count($answers) > 0){
				$html = '';
				$html .= '<h4>Replies</h4>';
				$i=1;
				foreach ($answers as $answer) {
					$html .= '<p>'.$i.'. '.$answer->answer.'<span class="pull-right">Reply by: <a>'.$answer->name.'</a></span></p>';
					$i++;
				}
				$response = [
	                'status'  => true,
	                'data' => $html,
	                'msg' => "Success"
	            ];
			}else{
				$response = [
	                'status'  => false,
	                'msg' => "No replies to this question."
	            ];
			}
			
		}else{
			$response = [
                'status'  => false,
                'msg' => "Question number is required"
            ];
		}
		return response()->json($response,$statusCode);
	}

	/**
	 * Post answer to correponding questions by an user.
	 *	It's ajax function
	 * @return Response
	*/
	public function addAnswer(Request $request){
		//print_r(json_encode(array("status"=>true)));
		$statusCode = 200;
        $response = [
            'status'  => false, 
            'msg' => ""
        ];
        //echo $request->input('questionid');exit;
		if($request->input('questionid') != ''){
			$answerData = array(
				"answer" => $request->input('answer'),
				"question_id" => $request->input('questionid'),
				"user_id"	 => Auth::user()->id
			);
			$addAnswer = Answer::create($answerData);
			if($addAnswer){
				$response = [
	                'status'  => true,
	                'msg' => "Added successfully"
	            ];
			}else{
				$response = [
	                'status'  => false,
	                'msg' => "Please try after some time"
	            ];
			}
		}else{
			$response = [
                'status'  => false,
                'msg' => "Question number is required"
            ];
		}
		return response()->json($response,$statusCode);
	}

}
