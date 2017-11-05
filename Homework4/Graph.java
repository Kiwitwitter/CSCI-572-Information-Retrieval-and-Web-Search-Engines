package edu.usc.csci572;

import java.util.HashSet;
import java.util.Set;

public class Graph {
	private HashSet<Node> nodes;
	public Graph() {
		this.nodes = new HashSet<>();
	}
	
	public void addToGraph(Node n) {
		nodes.add(n);
	}
	
	public Set<Node> getGraphNodes(){
		return this.nodes;
	}
}
