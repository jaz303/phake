<?php
desc('Show some colors');
task('colors', function() {
	writeln('Running a test of color codes...');
	writeln(
			red('Red star,'), "\n",
			green('green leaf,'), "\n",
			blue('blue sky,'), "\n",
			yellow('yellow stone,'), "\n",
			cyan('cyanide,'), "\n",
			black('black hole,'), "\n",
			purple('purple rain,'), "\n",
			white('"bolded text"', true),
			green("and a text with \nnew line!")
	);
});