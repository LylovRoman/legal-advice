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
    public function show($issue) {
        $issue = Issue::query()->find($issue);
        $issue->client = $issue->client;
        if (isset($issue->lawyer_id)){
            $issue->lawyer = $issue->lawyer;
        } else {
            $issue->lawyer = '---';
        }
        if ($issue) {
            return response()->json([
                "data" => $issue
            ]);
        }
        throw new HttpResponseException(response()->json([
            "error" => [
                "code" => 404,
                "message" => "Issue not found"
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
            $issues = Issue::query()
                ->where('client_id', Auth::id())
                ->get();
            $otherIssues = Issue::query()
                ->where('client_id', '!=', Auth::id())
                ->get()
                ->shuffle();
            $clientsBeen = [];
            foreach ($otherIssues as $issue) {
                if (!in_array($issue->client_id, $clientsBeen)) {
                    $issues->push($issue);
                }
                $clientsBeen[] = $issue->client_id;
            }
            foreach ($issues as $key => $issue) {
                $issue->client = $issue->client;
                if (isset($issue->lawyer_id)){
                    $issue->lawyer = $issue->lawyer;
                } else {
                    $issue->lawyer = '---';
                }
                $issues[$key] = $issue;
            }
        } else {
            $otherIssues = Issue::query()->where('status', 'new');
            $issues = Issue::query()
                ->where('lawyer_id', Auth::id());
            if (isset($_GET['status'])) {
                $issues = $issues->where('status', $_GET['status']);
                $otherIssues = $otherIssues->where('status', $_GET['status']);
            }
            if (isset($_GET['from']) && isset($_GET['to'])) {
                $issues = $issues->whereBetween('created_at', [$_GET['from'], $_GET['to']]);
                $otherIssues = $otherIssues->whereBetween('created_at', [$_GET['from'], $_GET['to']]);
            }
            $issues = $issues->orderByDesc('created_at')->get();
            $otherIssues = $otherIssues->orderByDesc('created_at')->get();
            $issues = $issues->merge($otherIssues);
        }
        $issues = $issues->sortByDesc('created_at', SORT_NATURAL);
        foreach ($issues as $key => $issue) {
            $issue->client = $issue->client;
            if (isset($issue->lawyer_id)){
                $issue->lawyer = $issue->lawyer;
            } else {
                $issue->lawyer = '---';
            }
            $issues[$key] = $issue;
        }
        $issues = array_values($issues->toArray());
        return response()->json([
        "data" => [
            "issues" => $issues
        ]
    ]);
    }
}
