#!/bin/bash

PAGENAME="Random Image Block plugin for WordPress"
README="readme.md"

rm -f index.html $README
cat header > index.html && \
  git show master:$README > $README && \
  sed -i "/# $PAGENAME/c <div id=\"title\"><h1>mattrude.github.com <i>&mdash; </i>$PAGENAME</h1></div><p><a href=\"/\">mattrude.github.com</a> / <strong>$PAGENAME</strong></p>" $README && \
  markdown $README >> index.html && \
  cat footer >> index.html
rm -f $README
