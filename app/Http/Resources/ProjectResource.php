<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'abstract' => $this->abstract,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'user' => new UserResource($this->user),

            'category' => new CategoryResource($this->category),
            'category_id'=> $this->category_id,

            'featured' => $this->when(1, function () {
                if (count($this->getMedia('featured')) > 0) {
                    return MediaResource::collection($this->getMedia('featured'));

                } else {
                    $fake['name'] = "noimage";
                    $fake['url'] = "https://kcl-mrcdtp.com/wp-content/uploads/sites/201/2017/05/person_icongray-300x300.png";
                    $return[] = $fake;
                    return $return;
                }
            }),
        ];
    }
}
