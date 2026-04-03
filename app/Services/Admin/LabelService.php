<?php

namespace App\Services\Admin;

use App\Models\Label;
use Illuminate\Database\Eloquent\Collection;

class LabelService
{
    public function all(): Collection
    {
        return Label::withCount('products')->orderBy('name')->get();
    }

    public function create(array $data): Label
    {
        return Label::create($data);
    }

    public function update(Label $label, array $data): void
    {
        $label->update($data);
    }

    public function delete(Label $label): void
    {
        $label->delete();
    }
}
