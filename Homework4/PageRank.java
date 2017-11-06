package edu.usc.csci572;

import java.io.BufferedWriter;
import java.io.FileWriter;

public class PageRank {
	public static void calculate(Graph g) {
		int sum = g.getGraphNodes().size();
		double initial_score = ((double)1/(double)sum);
		double d_factor = 0.85;
		int max_loop = 30;
		int total = 0;
		for(Node n: g.getGraphNodes()) {
			total += n.getOutLinkSize();
			n.setScore(initial_score);
		}
//		System.out.println("Total Num of Links" + total);
		int i = 0;
		while(i < max_loop) {
			for(Node cur: g.getGraphNodes()) {
				double new_rank = 0;
				for(Node income: cur.getInLink()) {
					new_rank += (income.getScore()/income.getOutLinkSize());
				}
				new_rank = (1-d_factor) + (d_factor*new_rank);
				cur.setScore(new_rank);
			}
			i++;
		}
	}
	
	public static void outPutLink(Graph g) {
		try {
			FileWriter out = new FileWriter("/usr/local/solr-7.1.0/server/solr/boston_globe/data/link.txt");
			BufferedWriter bw = new BufferedWriter(out);
			for(Node node:g.getGraphNodes()) {
				bw.append("In link for" + "http://www.bostonglobe.com/" + node.getId() + " " + node.getInLinkSize());
				bw.newLine();
				bw.append("Out link for" + "http://www.bostonglobe.com/"+ node.getId() + " " + node.getOutLinkSize());
				bw.newLine();
				bw.newLine();
				bw.flush();
			}
			bw.close();
			out.close();
		}catch(Exception e) {
			e.printStackTrace();
		}
	}
	
	public static void outPutPageRank(Graph g) {
		try {
			FileWriter out = new FileWriter("/usr/local/solr-7.1.0/server/solr/boston_globe/data/pagerank.txt");
			BufferedWriter bw = new BufferedWriter(out);
			for(Node node: g.getGraphNodes()) {
				bw.append("/usr/local/solr-7.1.0/server/solr/boston_globe/data/BG/" + node.getId() +"="+Double.toString(node.getScore()));
				bw.newLine();
			}
			bw.close();
			out.close();
		}catch(Exception e) {
			e.printStackTrace();
		}
		
	}
	
	public static void main(String [] args) {
		ExtractLinks el = new ExtractLinks();
		el.readCSV("/usr/local/solr-7.1.0/server/solr/boston_globe/data/Boston Global Map.csv");
		Graph g = el.buildGraph("/usr/local/solr-7.1.0/server/solr/boston_globe/data/BG");
		calculate(g);
		outPutLink(g);
		outPutPageRank(g);
	}
}
