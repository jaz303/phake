#!/bin/bash
_phake()
{
    COMPREPLY=()

    local cur prev
    cur="${COMP_WORDS[COMP_CWORD]}"
    prev="${COMP_WORDS[COMP_CWORD-1]}"

    local opts
    opts=`phake -T | awk 'BEGIN {x=1}; $x>1 {print $1}'`
    optscolon=${cur%"${cur##*:}"}

    COMPREPLY=( $(compgen -W "${opts}"  -- ${cur}))

    local i=${#COMPREPLY[*]}
    while [ $((--i)) -ge 0 ]; do
        COMPREPLY[$i]=${COMPREPLY[$i]#"$optscolon"}
    done
}

complete -F _phake phake
