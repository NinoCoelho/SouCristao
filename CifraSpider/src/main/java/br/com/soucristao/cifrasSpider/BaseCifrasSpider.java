package br.com.soucristao.cifrasSpider;

import java.io.IOException;
import java.net.MalformedURLException;
import java.net.UnknownHostException;

import org.xml.sax.SAXException;

import com.meterware.httpunit.WebConversation;
import com.meterware.httpunit.WebLink;
import com.meterware.httpunit.WebResponse;

public abstract class BaseCifrasSpider {

	protected WebConversation wc;

	public abstract void start();

	public BaseCifrasSpider() {
		super();
	}

	protected WebResponse safeClick(WebLink link) throws IOException,
			SAXException {
		String target = link.getTarget();
		try {
			link.click();
		} catch (RuntimeException e) {
		} catch (UnknownHostException e) {
		}
		return wc.getCurrentPage();
	}

	protected WebResponse safeLoadPage(String pageURL)
			throws MalformedURLException, IOException, SAXException {
		try {
			wc.getResponse(pageURL);
		} catch (RuntimeException e) {
		} catch (UnknownHostException e) {
		}
		return wc.getCurrentPage();
	}

}