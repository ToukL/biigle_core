<?php

namespace Dias\Policies;

use Dias\ImageLabel;
use Dias\User;
use Dias\Role;
use DB;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImageLabelPolicy extends CachedPolicy
{
    use HandlesAuthorization;

    /**
     * Intercept all checks
     *
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before($user, $ability)
    {
        if ($user->isAdmin) {
            return true;
        }
    }

    /**
     * Determine if the user can delete the given image label.
     *
     * If the user created the image label, they must be editor or admin of one
     * of the projects, the image belongs to. If another user created it, they must
     * be admin of one of the projects.
     *
     * @param  User  $user
     * @param  ImageLabel  $imageLabel
     * @return bool
     */
    public function destroy(User $user, ImageLabel $imageLabel)
    {
        return $this->remember("image-label-can-destroy-{$user->id}-{$imageLabel->id}", function () use ($user, $imageLabel) {
            // selects the IDs of the projects, the image belongs to
            $projectIdsQuery = function ($query) use ($imageLabel) {
                $query->select('project_transect.project_id')
                    ->from('project_transect')
                    ->join('images', 'project_transect.transect_id', '=', 'images.transect_id')
                    ->where('images.id', $imageLabel->image_id);
            };

            if ((int) $imageLabel->user_id === $user->id) {
                // editors and admins may detach their own labels
                return DB::table('project_user')
                    ->where('user_id', $user->id)
                    ->whereIn('project_id', $projectIdsQuery)
                    ->whereIn('project_role_id', [Role::$editor->id, Role::$admin->id])
                    ->exists();
            } else {
                // only admins may detach labels other than their own
                return DB::table('project_user')
                    ->where('user_id', $user->id)
                    ->whereIn('project_id', $projectIdsQuery)
                    ->where('project_role_id', Role::$admin->id)
                    ->exists();
            }
        });
    }
}