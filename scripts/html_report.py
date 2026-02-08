#!/usr/bin/python2.7

import sys
import os
import random
from datetime import datetime
from collections import defaultdict
report_path = sys.argv[1]
files = sorted(os.listdir(report_path))
file_groups = defaultdict(list)
for file in files:
    datestamp = datetime.fromtimestamp(int(file)).strftime('%Y-%m-%d')
    file_groups[datestamp].append(file)

nodes = list()

html_top = '''
<html>
<head>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.7.0/jquery.modal.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.7.0/jquery.modal.min.css">
	<style type="text/css">
        	.miss { background-image: -webkit-repeating-radial-gradient(circle, red, white 40%, red 90%) }
        	.hit  { }
        	.req  { width: 16px; height: 16px; float: left }
        	.fail { color: red; background-color: white; text-align: center }
		a { text-decoration: none }
		body { font-family: Arial }
                .dategroup { display: block; width: 1100px; }
	</style>
</head>
<body>
	<div class="main">
'''
report_output = report_path.replace('/','-')+'.html'
h = open(report_output, 'w')
h.write(html_top)
#for datestamp, files in file_groups.iteritems():
#    h.write("\t<div>{0}</div><div class='dategroup'>".format(datestamp))
for file in files:
    filename = os.path.join(report_path, file)
    f = open(filename, 'r')
    header = dict()
    for line in f.readlines():
        if '< ' not in line:
            continue
        try:
            (k, v) = line.strip().replace('< ','').split(': ', 1)
            if k == 'Powered-By-ChinaCache':
                k = 'X-Cache'
            if k not in header:
                header[k] = v
            else:
                if k == 'X-Cache' and 'HIT' in v:
                    header[k] = v
        except:
            continue
    f.close()

    node = 'default'
    if 'X-Served-By' in header:
        node = header['X-Served-By']
    elif 'X-CDN' in header:
        if header['X-CDN'] == 'LLNW':
            node = header['X-Cache'].split(' ')[2].split('.')[0]
        elif header['X-CDN'] == 'ECST':
            if 'ECS' in header['Server']:
                node = header['Server'].split(' ')[1].replace('(','').replace(')','').replace('/','_')
        else:
            node = header['X-Cache'].split(' ')[2].split('.')[0]
    else:
        node = ''

    node_class = node.replace('-','')
    try:
        if 'MISS' in header['X-Cache']:
            div_class = 'miss'
        elif 'HIT' in header['X-Cache']:
            div_class = 'hit'
    except:
        div_class = 'unknown'

    content = '&nbsp;'
    if node == '':
        div_class = 'fail'
        content = '&#10006;'

    h.write("<a href='results.php?file={0}' rel='modal:open' class='req {1} {2}'>{3}</a>\n".format(filename, div_class, node_class, content))
    nodes.append(node)
    # h.write("</div>\n")

# http://htmlcolorcodes.com/
hex_colors = [
    '#48c9b0', '#1abc9c', '#17a589', '#148f77', '#117864', '#0e6251',
    '#45b39d', '#16a085', '#138d75', '#117a65', '#0e6655', '#0b5345',
    '#52be80', '#27ae60', '#229954', '#1e8449', '#196f3d', '#145a32', 
    '#58d68d', '#2ecc71', '#28b463', '#239b56', '#1d8348', '#186a3b',
]

h.write('''
	</div>
	<style type="text/css">
''')
nodes = list(set(nodes))
for node in nodes:
    i = nodes.index(node)
    node_class = node.replace('-','')
    while i > (len(hex_colors) - 1):
        i = i - (len(hex_colors) - 1)
    hex_color = hex_colors[i]
    h.write("\t\t")
    h.write('.%s { background-color: %s }' % (node_class, hex_color))
    h.write("\n")

h.write('''
		.modal { width: 1100px; }
	</style>
</body>
</html>
''')
h.close()

