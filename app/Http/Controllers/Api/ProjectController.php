<?php namespace Dias\Http\Controllers\Api;

use Dias\Project;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProjectController extends Controller {

	/**
	 * Shows all projects the requesting user belongs to.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return $this->user->projects;
	}

	/**
	 * Displays the specified project.
	 *
	 * @param  int  $id
	 * @return Project
	 */
	public function show($id)
	{
		$project = $this->requireNotNull(Project::find($id));
		$this->requireCanSee($project);
		
		return $project;
	}

	/**
	 * Updates the attributes of the specified project.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($id)
	{
		$project = $this->requireNotNull(Project::find($id));
		$this->requireCanAdmin($project);
		
		$project->name = $this->request->input('name', $project->name);
		$project->description = $this->request->input('description', $project->description);
		$project->save();
	}

	/**
	 * Creates a new project.
	 *
	 * @return Project
	 */
	public function store()
	{
		$this->validate($this->request, Project::$createRules);

		$project = new Project;
		$project->name = $this->request->input('name');
		$project->description = $this->request->input('description');
		$project->setCreator($this->user);
		$project->save();

		if ($this->isAutomatedRequest($this->request))
		{
			return $project->fresh();
		}

		return redirect()->route('home')
			->with('message', 'Project '.$project->name.' created')
			->with('messageType', 'success');
	}

	/**
	 * Removes the specified project.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{

		$project = $this->requireNotNull(Project::find($id));
		$this->requireCanAdmin($project);

		try
		{
			$project->removeAllTransects($this->request->has('force'));
		}
		catch (HttpException $e)
		{
			if ($this->isAutomatedRequest($this->request))
			{
				abort(400, $e->getMessage());
			}

			return redirect()->back()
				->with('message', $e->getMessage())
				->with('messageType', 'danger');
		}
		$project->delete();

		if ($this->isAutomatedRequest($this->request))
		{
			return response('Deleted.', 200);
		}

		return redirect()->route('home')
			->with('message', 'Project '.$project->name.' deleted')
			->with('messageType', 'success');
	}

}
