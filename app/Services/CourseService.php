<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class CourseService
{
    public function saveCourse(array $data, ?Course $course = null): Course
    {
        return DB::transaction(function () use ($data, $course) {
            
            $attributes = $this->prepareAttributes($data, $course);

            if ($course) {
                $course->update($attributes);
            } else {
                $course = Course::create($attributes);
            }

            // Sync curriculum (pass empty array if nothing sent to clear items)
            $this->syncCurriculum($course, $data['items'] ?? []);

            return $course;
        });
    }

    protected function prepareAttributes(array $data, ?Course $course = null): array
    {
        $fields = [
            'course_type_id', 'title', 'slug', 'summary', 'description',
            'level', 'status', 'published_at', 'is_featured'
        ];

        // Format price to cents safely
        if (array_key_exists('price', $data)) {
            $data['price'] = empty($data['price']) ? 0 : (int) round($data['price'] * 100);
            $fields[] = 'price';
        }

        // Handle Thumbnail Upload & Disk Cleanup
        if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
            // Delete old file to prevent storage bloat
            if ($course && $course->getRawOriginal('thumbnail_url')) {
                Storage::disk('public')->delete($course->getRawOriginal('thumbnail_url'));
            }
            
            $data['thumbnail_url'] = $data['thumbnail']->store('courses/thumbnails', 'public');
            $fields[] = 'thumbnail_url';
        }

        // Generate Code on Creation
        if (!$course) {
            $data['code'] = $this->generateNextCode();
            $fields[] = 'code';
        }

        return Arr::only($data, $fields);
    }

    protected function syncCurriculum(Course $course, array $items): void
    {
        $keepIds = [];

        foreach ($items as $index => $itemData) {
            $type = $itemData['type'] ?? 'section'; 

            // Save Root Item
            $rootItem = $course->curriculumItems()->updateOrCreate(
                ['id' => $itemData['id'] ?? null],
                [
                    'parent_id'  => null,
                    'type'       => $type,
                    'title'      => $itemData['title'],
                    'slug'       => Str::slug($itemData['title']),
                    'sort_order' => $index,
                    'is_active'  => $itemData['is_active'] ?? true,
                    'content'    => [
                        'description' => $itemData['description'] ?? null,
                        'html'        => $itemData['html_content'] ?? null,
                    ]
                ]
            );

            $keepIds[] = $rootItem->id;

            // Save Children
            if ($type === 'section' && !empty($itemData['children'])) {
                foreach ($itemData['children'] as $childIndex => $childData) {
                    $childItem = $course->curriculumItems()->updateOrCreate(
                        ['id' => $childData['id'] ?? null],
                        [
                            'parent_id'  => $rootItem->id,
                            'type'       => 'lesson',
                            'title'      => $childData['title'],
                            'slug'       => Str::slug($childData['title']),
                            'sort_order' => $childIndex,
                            'is_active'  => $childData['is_active'] ?? true,
                            'content'    => [
                                // FIX: Description is now correctly saved for children
                                'description' => $childData['description'] ?? null,
                                'html'        => $childData['html_content'] ?? null
                            ]
                        ]
                    );
                    $keepIds[] = $childItem->id;
                }
            }
        }

        // Delete removed items
        if (!empty($keepIds)) {
             $course->curriculumItems()->whereNotIn('id', $keepIds)->delete();
        } else {
             $course->curriculumItems()->delete(); // Safely wipe if all removed
        }
    }

    public function generateNextCode(): string
    {
        $year = date('Y');
        $prefix = "CRS-{$year}-";

        // FIX: lockForUpdate prevents race conditions generating the same code simultaneously
        $lastCode = Course::withTrashed()
            ->where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->lockForUpdate() 
            ->value('code');

        $nextNumber = 1;
        if ($lastCode && preg_match('/-(\d+)$/', $lastCode, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}