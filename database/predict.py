#!/usr/bin/env python
import sys
import json
import numpy
from sklearn.semi_supervised import LabelSpreading

def main(argv):
	inactive = "inactive"
	active = "active"
	#print "inside python script"
	label_prop_model = LabelSpreading(alpha=0.8, tol=0.7)
	
	#TODO: test json decoding and argv index
	#print "\n data_pre: " + json.loads(argv[0])[0]
	data_pre = json.loads(json.dumps(eval(argv[1])))
	#print argv[2]
	labels_pre = argv[2]
	#print argv[3]
	raw_data = json.loads(json.dumps(eval(argv[3])[0]))
	#print type(raw_data)
	print raw_data

	data_post = {} # dict of all preprocessed data ts[]:(average x,y,z and standard of deviation)
	labels_post = [] # list of labels corresponding to data in data_post
	unlabeled_post = {} # dict of unlabeled preprocessed data ts[]:(average x,y,z and standard of deviation)

	# separate into 5-second pieces of data 
	five_sec = [] # list of lists of keys within five seconds of each other; ie datapoints
	i=0
	while i < len(raw_data.keys()):
		curr_five = []
		dat = int(raw_data.keys()[i])
		#print type(dat)
		#print dat
		count=0
		for curr_dat_u in raw_data.keys():
			curr_dat=int(curr_dat_u)
			if dat<=curr_dat and dat+5>=curr_dat: #NOTE: timestamps, so the add 5 might not work
				curr_five.append(curr_dat)
				count+=1
		five_sec.append([curr_dat_u])
		i+=count

	# get the data
	for five in five_sec:
		print five
		xl = [] # list of x accelerations
		yl = [] # list of y accelerations
		zl = [] # list of z accelerations

		# get averages and standards of deviation
		for f in five:
			raw = raw_data[f]
			xl.append(raw[0])
			yl.append(raw[1])
			zl.append(raw[2])
			dats = [numpy.mean(xl), numpy.mean(yl), numpy.mean(zl),numpy.std(xl),numpy.std(yl),numpy.std(zl)]
		data_post[five] = dats

		# get label
		for i in range(len(data_pre)):
			label = -1 #default label
			if five[0][0]>=data_pre[i][0] and five[4][1]<=data_pre[i][1]:
				label = labels_pre[i]
		labels_post.append(label)
		
		# if unlabeled, add to unlabled data
		if label == -1:
			unlabeled_post[five] = dats
		
	# train on the data
	label_prop_model.fit(data_post.values(), labels_post)
	# predict unlabeled points
	labels_predicted = label_prop_model.predict(unlabeled_post.values())


if __name__ == "__main__":
   main(sys.argv)
