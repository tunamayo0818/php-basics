<?php
class Challenge extends Model
{
    protected $table = 'challenges';

    protected $type;

    protected $fillable = ['type', 'goal_value', 'deadline', 'del_flg'];

    public function isContinuation(): bool
    {
        return $this->type === 0;
    }
}
