#!/usr/bin/env python
import sys
import json
from sklearn.semi_supervised import LabelSpreading

def main(argv):
	
	label_prop_model = LabelSpreading(alpha=0.8, tol=0.7)
	
	#TODO: test json decoding and argv index
	data_pre = json.loads(eval(sys.argv[0])[0]) 
	labels_pre = json.loads(eval(sys.argv[1])[0])
	raw_data = json.loads(eval(sys.argv[2])[0])

	#TODO: preprocess data...
	min_time = raw_data[0][0]
	max_time = raw_data[raw_data.length()-1][0]

	for dat in raw_data:



	label_prop_model.fit(data, labels)
	label_prop_model.predict(unlabeled_only)


f __name__ == "__main__":
   main(sys.argv[1:])
