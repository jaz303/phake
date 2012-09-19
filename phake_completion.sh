#!/bin/bash
_phake()
{
    COMPREPLY=()

    local cur prev
    _get_comp_words_by_ref -n : cur prev

    local opts
    opts=`phake -T | awk 'BEGIN {x=1}; $x>1 {print $1}'`

    COMPREPLY=( $(compgen -W "${opts}"  -- ${cur}))

    __ltrim_colon_completions "$cur"
}

complete -F _phake phake
