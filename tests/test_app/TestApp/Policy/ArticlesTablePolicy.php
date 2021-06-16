<?php

namespace TestApp\Policy;

use Phauthentic\Authorization\IdentityInterface;
use Cake\Datasource\QueryInterface;

class ArticlesTablePolicy
{
    public function canIndex(IdentityInterface $identity)
    {
        return $identity['can_index'];
    }

    public function canEdit(IdentityInterface $identity)
    {
        return $identity['can_edit'];
    }

    public function canModify(IdentityInterface $identity)
    {
        return $identity['can_edit'];
    }

    public function scopeEdit(IdentityInterface $user, QueryInterface $query)
    {
        return $query->where([
            'user_id' => $user['id']
        ]);
    }

    public function scopeModify(IdentityInterface $user, QueryInterface $query)
    {
        return $query->where([
            'identity_id' => $user['id']
        ]);
    }
}
