#!/usr/bin/env python

import sys
import os
report_path = sys.argv[1]
files = os.listdir(report_path)
nodes = list()
for file in files:
    filename = os.path.join(report_path, file)
    f = open(filename, 'r')
    header = dict()
    for line in f.readlines():
        try:
            (k, v) = line.strip().replace('< ','').split(': ', 1)
            header[k] = v
        except:
            continue
    f.close()

    node = ''
    try:
      if header['X-CDN'] == 'LLNW':
          node = header['X-Cache'].split(' ')[2].split('.')[1]
      else:
          node = header['X-Cache'].split(' ')[2].split('.')[0]
    except:
      if 'X-Served-By' in header:
          node = header['X-Served-By']
    nodes.append(node)

nodes = set(nodes)

rows = list()
for file in files:
    filename = os.path.join(report_path, file)
    f = open(filename, 'r')
    header = {'X-Served-By': ''}
    for line in f.readlines():
        try:
            (k, v) = line.strip().replace('< ','').split(': ', 1)
            header[k] = v
        except:
            continue

    row = [file]
    for node in nodes:
       status = ''
       if node in header['X-Cache'] or node in header['X-Served-By']:
           if 'MISS' in header['X-Cache']:
               status = '1'
           elif 'HIT' in header['X-Cache']:
               status = '2'
           else:
               status = ''
       row.append(status)

    rows.append(row)
    f.close()

report_output = report_path.replace('/','-')+'.csv'
f = open(report_output, 'w')
f.write("time,{0}\n".format(','.join(nodes)))
for row in rows:
    f.write(','.join(row) + "\n")
f.close()
