<?php

namespace TestApp\Policy;

use Phauthentic\Authorization\Policy\Result;
use TestApp\Model\Entity\Article;

class ArticlePolicy
{

    /**
     * Create articles if you're an admin or author
     *
     * @param \Phauthentic\Authorization\IdentityInterface $user
     * @return bool
     */
    public function canAdd($user)
    {
        return new Result(in_array($user['role'], ['admin', 'author']));
    }

    public function canEdit($user, Article $article)
    {
        if (in_array($user['role'], ['admin', 'author'])) {
            return new Result(true);
        }

        return new Result($article->get('user_id') === $user['id']);
    }

    public function canModify($user, Article $article)
    {
        if (in_array($user['role'], ['admin', 'author'])) {
            return new Result(true);
        }

        return new Result($article->get('user_id') === $user['id']);
    }

    /**
     * Delete only own articles or any if you're an admin
     *
     * @param \Phauthentic\Authorization\IdentityInterface $user
     * @param Article $article
     * @return bool
     */
    public function canDelete($user, Article $article)
    {
        if ($user['role'] === 'admin') {
            return new Result(true);
        }

        return new Result($user['id'] === $article->get('user_id'));
    }

    /**
     * Scope method for index
     *
     * @param \Phauthentic\Authorization\IdentityInterface $user
     * @param Article $article
     * @return bool
     */
    public function scopeIndex($user, Article $article)
    {
        $article->user_id = $user->getOriginalData()['id'];
        return $article;
    }

    /**
     * Testing that the article can be viewed if its public and no user is logged in
     *
     * This test "null" user cases
     *
     * @param \Phauthentic\Authorization\IdentityInterface|null $user
     * @param Article $article
     * @return bool
     */
    public function canView($user, Article $article)
    {
        if ($article->get('visibility') !== 'public' && empty($user)) {
            return new Result(false);
        }

        return new Result(true);
    }

    /**
     * Testing that the article can be published
     *
     * This tests Result objects.
     *
     * @param \Authorization\IdentityInterface|null $user
     * @param Article $article
     * @return Result
     */
    public function canPublish($user, Article $article)
    {
        if ($article->get('visibility') === 'public') {
            return new Result(false, 'public');
        }

        return new Result($article->get('user_id') === $user['id']);
    }
}
