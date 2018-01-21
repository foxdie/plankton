<?php

$l = new SplDoublyLinkedList();

$l->push("a");$l->rewind();
$l->push("b");$l->rewind();
$l->push("c");$l->rewind();


while ($l->valid()){
	echo $l->current();
	$l->next();
}
		
