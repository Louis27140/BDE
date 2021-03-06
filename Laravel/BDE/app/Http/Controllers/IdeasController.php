<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Idee;
use App\Vote;

class IdeasController extends Controller
{
    /**
     *
     * display the ideas
     *
     */
    public function index() {
        $ideas = Idee::all();
        $votes = Vote::all();

        if(Auth::check()) {
            $ideas = $ideas->where("centre_id", Auth::user()->centre_id);
        } else {
            $ideas = $ideas->where("centre_id", env("centre_id", 1));
        }
        return view('ideas/index', compact('ideas', 'votes'));
    }

    /**
     *
     * display form to create an idea
     *
     */
    public function create() {
        return view('ideas/create');
    }

    /**
     *
     * display form to edit an idea
     *
     * @param $id
     *
     */
    public function edit($id) {
        $edit = Idee::find($id);
        return view('ideas/edit', compact('edit'));
    }


    /**
     *
     * display ideas searched by user
     *
     */
    public function searchIdea(Request $request) {
        $votes = Vote::all();

        $request->validate([
            'search' => 'max:40',
        ]);

        if($request->input('search') != null) {
            if(Auth::check()) {
                $ideas = Idee::where("nom", 'LIKE', '%' . $request->input('search') . '%')->where("centre_id", Auth::user()->centre_id)->orWhere("description", 'LIKE', '%' . $request->input('search') . '%')->get();
            } else {
                $ideas = Idee::where("nom", 'LIKE', '%' . $request->input('search') . '%')->where("centre_id", env("centre_id", 1))->orWhere("description", 'LIKE', '%' . $request->input('search') . '%')->get();
            }
            return view('ideas/index', compact('ideas', 'votes'));
        } else {
            return redirect('ideas');
        }
    }

    /**
     *
     * create an idea
     *
     * @param Request
     *
     */
    public function createIdea(Request $request) {

        $request->validate([
            'nom' => 'required|max:40',
            'description' => 'required|max:255',
        ]);
        $idea = new Idee;
        $idea->nom = $request->input('nom');
        $idea->description = $request->input('description');
        $idea->user_id = Auth::user()->id;
        $idea->centre_id = 1;
        $idea->save();
        return redirect('/ideas');
    }

    /**
     *
     * edit an idea
     *
     * @param Request
     * @param $id
     *
     */
    public function editIdea(Request $request, $id) {
        $request->validate([
            'nom' => 'required|max:40',
            'description' => 'required|max:255',
        ]);

        $idea = Idee::find($id);
        $idea->nom = $request->input('nom');
        $idea->description = $request->input('description');
        $idea->save();
        return redirect('/ideas');
    }


    /**
     *
     * delete an idea
     *
     * @param $id
     *
     */
    public function deleteIdea($id) {
        $delete = Idee::find($id);
        $delete->delete();
        return back();
    }

    /**
     *
     * vote for an idea
     *
     * @param $id
     *
     */
    public function addVote($id) {
        $vote = new Vote;
        $vote->idee_id = $id;
        $vote->user_id = Auth::user()->id;
        $vote->save();
        return redirect('/ideas#vote_' . $id);
    }

    /**
     *
     * downvote for an idea
     *
     * @param $id
     *
     */
    public function deleteVote($id) {
        Vote::where('user_id', Auth::user()->id)->where('idee_id', $id)->delete();

        return redirect('/ideas#vote_' . $id);
    }
}
