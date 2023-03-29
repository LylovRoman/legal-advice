<?php

namespace App\Http\Controllers;

use App\Http\Requests\IssueCommentRequest;
use App\Http\Requests\IssueQuestionRequest;
use App\Http\Requests\IssueResponseRequest;
use App\Models\Issue;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueController extends Controller
{
    public function question(IssueQuestionRequest $request)
    {
        $imagePath = null;
        if ($request->image) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $imagePath = '/images/' . $imageName;
        }
        $issue = Issue::query()->create([
            'category' => $request->category,
            'question' => $request->question,
            'image_path' => $imagePath,
            'status' => 'new',
            'client_id' => Auth::id()
        ]);
        return response()->json([
            "data" => [
                "code" => "Success"
            ]
        ]);
    }

    public function response(IssueResponseRequest $request, $issue)
    {
        $issue = Issue::query()->find($issue);
        if ($issue && ($issue->status == 'new')) {
            $issue->update([
                'response' => $request->response,
                'status' => 'in progress',
                'lawyer_id' => Auth::id()
            ]);
            return response()->json([
                "data" => [
                    "code" => "Success"
                ]
            ]);
        }
        throw new HttpResponseException(response()->json([
            "error" => [
                "code" => 404,
                "message" => "Issue not found or already in progress"
            ]
        ], 404));
    }

    public function comment(IssueCommentRequest $request, $issue)
    {
        $issue = Issue::query()->find($issue);
        if ($issue && ($issue->status == 'in progress') && ($issue->client_id == Auth::id())) {
            $issue->update([
                'comment' => $request->comment,
                'status' => 'completed'
            ]);
            return response()->json([
                "data" => [
                    "code" => "Success"
                ]
            ]);
        }
        throw new HttpResponseException(response()->json([
            "error" => [
                "code" => 404,
                "message" => "Issue not found or already completed"
            ]
        ], 404));
    }

    public function index()
    {
        $filter = $_GET['filter'] ?? false;
        $page = $_GET['page'] ?? 1;
        $limit = 3;
        $offset = $limit * ($page - 1);
        $maxPage = ceil(Issue::all()->count() / $limit);

        if (Auth::user()->role == 'client') {
            $issues = Issue::query()->orderBy('created_at')->get();
        }
        return response()->json([
        "data" => [
            "issues" => $issues
        ]
    ]);
    }
}
