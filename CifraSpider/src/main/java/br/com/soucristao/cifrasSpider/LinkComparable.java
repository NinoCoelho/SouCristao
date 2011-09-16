package br.com.soucristao.cifrasSpider;

import java.util.Comparator;

import com.meterware.httpunit.WebLink;

public class LinkComparable implements Comparator<WebLink> {
	private static LinkComparable instance;

	public static LinkComparable getInstance() {
		if (instance == null) {
			instance = new LinkComparable();
		}
		return instance;
	}

	private LinkComparable() {
	}

	public int compare(WebLink link, WebLink linkToCompare) {
		return link.getText().compareTo(linkToCompare.getText());
	}
}
