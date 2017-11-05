package edu.usc.csci572;

import java.util.ArrayList;
import java.util.List;

public class Node {
	private String link;
	private String id;
	private ArrayList<Node> in_link;
	private ArrayList<Node> out_link;
	private double score;
	
	public Node(String link,String id) {
		this.link = link;
		this.id = id;
		this.in_link = new ArrayList<>();
		this.out_link = new ArrayList<>();
		this.score = 0;
	}
	
	public void addInLink(Node inLink) {
		this.in_link.add(inLink);
	}
	
	public void addOutLink(Node outLink) {
		this.out_link.add(outLink);
	}
	
	public String getLink() {
		return this.link;
	}
	
	public String getId() {
		return this.id;
	}
	
	public List<Node> getInLink(){
		return this.in_link;
	}
	
	public List<Node> getOutLink(){
		return this.out_link;
	}
	
	public int getInLinkSize() {
		return this.in_link.size();
	}
	
	public int getOutLinkSize() {
		return this.out_link.size();
	}
	
	public void setScore(double s) {
		this.score = s;
	}
	
	public double getScore() {
		return this.score;
	}
}
