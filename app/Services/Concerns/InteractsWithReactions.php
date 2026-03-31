<?php

namespace App\Services\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait InteractsWithReactions
{
    protected function applyReactionSort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'best_rated' => $query
                ->orderByRaw('(likes_count - dislikes_count) DESC')
                ->orderByDesc('likes_count')
                ->latest(),
            'worst_rated' => $query
                ->orderByRaw('(dislikes_count - likes_count) DESC')
                ->orderByDesc('dislikes_count')
                ->latest(),
            default => $query->latest(),
        };
    }

    protected function decoratePaginatorWithReactions(
        LengthAwarePaginator $paginator,
        ?string $userId,
        string $reactionModel,
        string $foreignKey
    ): LengthAwarePaginator {
        $paginator->setCollection(
            $this->decorateCollectionWithReactions(
                $paginator->getCollection(),
                $userId,
                $reactionModel,
                $foreignKey
            )
        );

        return $paginator;
    }

    protected function decorateModelWithReaction(
        Model $model,
        ?string $userId,
        string $reactionModel,
        string $foreignKey
    ): Model {
        return $this->decorateCollectionWithReactions(
            collect([$model]),
            $userId,
            $reactionModel,
            $foreignKey
        )->first();
    }

    private function decorateCollectionWithReactions(
        Collection $models,
        ?string $userId,
        string $reactionModel,
        string $foreignKey
    ): Collection {
        if (!$userId || $models->isEmpty()) {
            return $models->each(
                fn (Model $model) => $model->setAttribute('my_reaction', null)
            );
        }

        $reactions = $reactionModel::query()
            ->where('user_id', $userId)
            ->whereIn($foreignKey, $models->pluck('id'))
            ->pluck('type', $foreignKey);

        return $models->each(function (Model $model) use ($reactions) {
            $model->setAttribute('my_reaction', $reactions[$model->id] ?? null);
        });
    }
}
