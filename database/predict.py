#!/usr/bin/env python
import sys
import json
import numpy
from sklearn.semi_supervised import LabelSpreading

def main(argv):
	inactive = "inactive"
	active = "active"
	#print "inside python script"
	label_prop_model = LabelSpreading(alpha=0.8, tol=0.7, gamma=0.5)
	
	#TODO: test json decoding and argv index
	#print "\n data_pre: " + json.loads(argv[0])[0]
	data_pre = json.loads(json.dumps(eval(argv[1])))
	#print argv[2]
	labels_pre = eval(argv[2])
	#print argv[3]
	raw_data = json.loads(json.dumps(eval(argv[3])))
	#print type(raw_data)
	#print raw_data

	data_post = {} # dict of all preprocessed data ts[]:(average x,y,z and standard of deviation)
	labels_post = [] # list of labels corresponding to data in data_post
	unlabeled_post = {} # dict of unlabeled preprocessed data ts[]:(average x,y,z and standard of deviation)

	# separate into 5-second pieces of data 
	five_sec = [] # list of lists of keys within five seconds of each other; ie datapoints
	i=0
	#print "raw_data length: " + str(len(raw_data.keys()))
	#print "raw_data keys: " + str(raw_data.keys())
	#print "raw_data: " + str(raw_data)
	while i < len(raw_data.keys()):
		#print "i= " + str(i)
		curr_five = []
		dat = int(raw_data.keys()[i])
		#print "dat= " + str(dat)
		#print type(dat)
		#print dat
		count=0
		for curr_dat_u in raw_data.keys():
			curr_dat=int(curr_dat_u)
			if dat<=curr_dat and dat+5>=curr_dat: #NOTE: timestamps, so the add 5 might not work
				#print "adding to five: " + str(curr_dat)
				curr_five.append(curr_dat)
				count+=1
				#print "count: " + str(count)
		if (len(curr_five)>0):
			five_sec.append(curr_five)
		#print "five_sec: " + str(five_sec)
		i+=count

	# get the data
	for five in five_sec:
		#print five
		xl = [] # list of x accelerations
		yl = [] # list of y accelerations
		zl = [] # list of z accelerations
		#print "five: " + str(five)
		tup = tuple(five)

		# get averages and standards of deviation
		for f in five:
			#print raw_data
			f=unicode(f)
			raw = raw_data[f]
			xl.append(raw[0])
			yl.append(raw[1])
			zl.append(raw[2])
			dats = [numpy.mean(xl), numpy.mean(yl), numpy.mean(zl),numpy.std(xl),numpy.std(yl),numpy.std(zl)]
		#print "assigning data_post " + str(tup)
		data_post[tup] = dats

		# get label
		#print "data_pre: " + str(data_pre)
		#print "length data_pre: " + str(len(data_pre))
		for i in range(len(data_pre)):
			label = -1 #default label
			#print "five: " + str(five)
			#print "data_pre[i]: " + str(data_pre[i])
			if five[0]>=data_pre[i][0] and five[0]<=data_pre[i][1]:
				#print "getting label for" + str(five[0])
				label = labels_pre[i]
				break
		#print "appending label" + str(label)
		labels_post.append(label)
		
		# if unlabeled, add to unlabled data
		if label == -1:
			#print "label -1: " + str(five)
			unlabeled_post[tup] = dats
		
	# train on the data
	#print "data_post.values(): "+ str(data_post.values())
	#print "labels_post length: " + str(len(labels_post))
	#print "data_post length: " + str(len(data_post.values()))
	data_train = numpy.array(data_post.values())
	#print data_train.shape
	labels_train = numpy.array(labels_post)
	#print labels_train.shape
	data_test = numpy.array(unlabeled_post.values())
	
	label_prop_model.fit(data_train, labels_train)
	# predict unlabeled points
	labels_predicted = (label_prop_model.predict(data_test)).tolist()
	#print labels_predicted
	labels_pred_final=[]
	unlabeled_post_final=[]

	for l in labels_predicted:
		if l==0:
			labels_pred_final.append("inactive")
		elif l==1:
			labels_pred_final.append("active")
		else:
			labels_pred_final.append("None")

	for ul in unlabeled_post.keys():
		unlabeled_post_final.append(json.dumps(ul))

	return_ls = [json.dumps(unlabeled_post_final),json.dumps(labels_pred_final)]
	print json.dumps(return_ls)
	#return json.dumps(return_ls)

if __name__ == "__main__":
   main(sys.argv)
