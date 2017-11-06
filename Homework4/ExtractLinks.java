package edu.usc.csci572;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.util.HashMap;
import java.util.Set;

import org.jsoup.*;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;


public class ExtractLinks {
	private HashMap<String, String> lk_id;
	private HashMap<String, Node> id_nd;
	private final String web = "http://www.bostonglobe.com";
	public ExtractLinks() {
		lk_id = new HashMap<>();
		id_nd = new HashMap<>();
	}
	
	public void readCSV(String file) {
		try {
			FileReader fl = new FileReader(file);
			BufferedReader bf = new BufferedReader(fl);
			String line = null;
			while((line = bf.readLine())!=null) {
				String [] content = line.split(",");
				lk_id.put(content[1].trim(), content[0].trim());
				Node node = new Node(content[1].trim(), content[0].trim());
				id_nd.put(content[0].trim(), node);
			}
			bf.close();
			fl.close();
		}catch(Exception e) {
			e.printStackTrace();
		}
	}
	
	public Graph buildGraph(String loc) {
		Graph g = new Graph();
		Set<String> keys = id_nd.keySet();
		for(String now: keys) {
			try {
				File nowf = new File(loc + "/" +now);
				Document doc = Jsoup.parse(nowf, "UTF-8", "http://www.bostonglobe.com/");
				Node nowNode = id_nd.get(now);
				g.addToGraph(nowNode);
				Elements links = doc.select("a[href]");
				Elements media = doc.select("src");
				Elements imports = doc.select("link[href]");
				HashMap<String, Integer> dupNode = new HashMap<>();
				for(Element link: links) {
					String fullLink = link.attr("href").trim();
					if(fullLink.length()>0 && fullLink.charAt(0)=='/') {
						fullLink = web + fullLink;
					}
					if(lk_id.containsKey(fullLink) && !dupNode.containsKey(fullLink)) {
						dupNode.put(fullLink, 1);
						Node temp = id_nd.get(lk_id.get(fullLink));
						nowNode.addOutLink(temp);
						temp.addInLink(nowNode);
					}
				}
				for(Element link: imports) {
					String fullLink = link.attr("href").trim();
					if(fullLink.length()>0 && fullLink.charAt(0)=='/') {
						fullLink = web + fullLink;
					}
					if(lk_id.containsKey(fullLink) && !dupNode.containsKey(fullLink)) {
						dupNode.put(fullLink, 1);
						Node temp = id_nd.get(lk_id.get(fullLink));
						nowNode.addOutLink(temp);
						temp.addInLink(nowNode);
					}
				}
				for(Element src: media) {
					String fullLink = src.attr("abs:src").trim();
					if(fullLink.length()>0 && fullLink.charAt(0)=='/') {
						fullLink = web + fullLink;
					}
					if(lk_id.containsKey(fullLink) && !dupNode.containsKey(fullLink)) {
						dupNode.put(fullLink, 1);
						Node temp = id_nd.get(lk_id.get(fullLink));
						nowNode.addOutLink(temp);
						temp.addInLink(nowNode);
					}
				}
				
			}catch(Exception e) {
				e.printStackTrace();
			}
		}
		return g;
	}
}
