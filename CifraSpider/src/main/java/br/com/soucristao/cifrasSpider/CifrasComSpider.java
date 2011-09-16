package br.com.soucristao.cifrasSpider;

import static java.lang.System.out;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.ResourceBundle;

import org.xml.sax.SAXException;

import com.meterware.httpunit.HttpUnitOptions;
import com.meterware.httpunit.TableCell;
import com.meterware.httpunit.WebConversation;
import com.meterware.httpunit.WebLink;
import com.meterware.httpunit.WebResponse;
import com.meterware.httpunit.WebTable;

public class CifrasComSpider extends BaseCifrasSpider {

	private static final String BASE_URL = "http://www.cifras.com.br/";

	/**
	 * @param args
	 * @throws IOException
	 * @throws SAXException
	 * @throws MalformedURLException
	 */
	public static void main(String[] args) throws MalformedURLException,
			SAXException, IOException {
		CifrasComSpider spider = new CifrasComSpider();
		spider.start();
	}

	public CifrasComSpider() {
		HttpUnitOptions.setExceptionsThrownOnErrorStatus(false);
		HttpUnitOptions.setExceptionsThrownOnScriptError(false);
		HttpUnitOptions.setImagesTreatedAsAltText(true);
		wc = new WebConversation();
	}

	private void getAndSaveMusic(WebLink musicLink) throws IOException,
			SAXException {
		String fileName = musicLink.getURLString();
		fileName = fileName.substring(fileName.lastIndexOf('/')+1);
		fileName = fileName.replace(".htm", ".chopro");
		File file = new File(fileName);
		if (!file.exists()) {
			extractAndSaveMusicAsChordPro(safeLoadPage(BASE_URL
					+ musicLink.getURLString()), file);
			System.out.println("Saved as: "+file.getAbsolutePath());
		}
	}

	private void extractAndSaveMusicAsChordPro(WebResponse musicResponse,
			File file) throws SAXException, IOException {
		String title = musicResponse.getText();
		title = title.substring(title.indexOf("cifra_mail.aspx"));
		title = title.substring(title.indexOf("assunto=") + 8);
		String author = title.substring(0, title.indexOf("@"));
		title = title.substring(title.indexOf("@"));
		title = title.substring(1, title.indexOf("'"));
		if (!file.exists()) {
			String chordProText = processMusic(musicResponse.getText()).trim();
			if (!chordProText.equals("")) {
				FileWriter fw = new FileWriter(file);
				fw.write("{t: " + title + "}\n{st: " + author + "}\n\n"
						+ chordProText);
				fw.close();
			}
		}
	}

	public List<WebLink> getArtistLinksList(WebTable table)
			throws MalformedURLException, SAXException, IOException {
		out.println("\t\tExtraindo links da tabela de artistas");
		TableCell cell;
		List<WebLink> links = new ArrayList<WebLink>();
		for (int i = 0; i < table.getColumnCount(); i++) {
			cell = table.getTableCell(0, i);
			links.addAll(Arrays.asList(cell.getLinks()));
		}
		return links;
	}

	public WebResponse getArtistListPage() throws MalformedURLException,
			IOException, SAXException {
		out.println("Obtendo página contendo tabela de artistas");
		return safeLoadPage(BASE_URL + "/artistas/idcategoria/12.htm");
	}

	public WebTable getArtistListTable(WebResponse response)
			throws SAXException, MalformedURLException, IOException {
		out.println("\tExtraindo tabela de artistas da página");
		return response.getTableStartingWithPrefix("\"Caedmon's Call\"");
	}

	public List<WebLink> getArtistMusicLinkListFromPage(WebResponse response,
			String artistName) throws SAXException, MalformedURLException,
			IOException {
		List<WebLink> linkList = new ArrayList<WebLink>();
		for (WebLink link : response.getLinks()) {
			if (link.getText().length() == 0
					&& link.getURLString().matches(
							"cifra/idmusica/[0-9]*[.]htm")) {
				out.println("\t\tAdicionando link para música: "
						+ link.getURLString());
				linkList.add(link);
			} else if (link.getText().equalsIgnoreCase("Proxima Pagina")) {
				out.println("\t\tAcessando próxima página");
				linkList.addAll(getMusicLinkList(Arrays
						.asList(new WebLink[] { link }), artistName));
			}
		}
		return linkList;
	}

	public List<WebLink> getMusicLinkList(List<WebLink> links, String artistName)
			throws MalformedURLException, SAXException, IOException {
		List<WebLink> linkList = new ArrayList<WebLink>();
		for (WebLink link : links) {
			out.println("Artista: " + link.getText());
			WebResponse response = safeLoadPage(BASE_URL + link.getURLString());
			linkList.addAll(getArtistMusicLinkListFromPage(response,
					artistName == null ? link.getText() : artistName));
		}
		return linkList;
	}

	public List<WebLink> getMusicLinkList(WebLink link)
			throws MalformedURLException, SAXException, IOException {
		return getMusicLinkList(Arrays.asList(new WebLink[] { link }), null);
	}

	public static final String START_CIFRA_STRING = "var cifra = \"";

	private String processMusic(String contents) {
		String result = "", chordLine = null;
		int pos = contents.indexOf(START_CIFRA_STRING), index;
		contents = contents.substring(pos + START_CIFRA_STRING.length(),
				contents.indexOf('\n', pos) - 2);
		contents = contents.replace("\\n", "\n");
		contents = contents.replaceAll("<.*?>", "");
		out.println(contents);
		for (String line : contents.split("\n")) {
			if (line.indexOf("[") != -1) {
				chordLine = line.replace("[", "").replace("]", "");
			} else {
				if (chordLine != null) {
					line += "                                                ";
					index = chordLine.length() - 1;
					chordLine += " ";
					while (index >= 0) {
						if (chordLine.charAt(index) == ' '
								&& chordLine.charAt(index + 1) != ' ') {
							line = line.substring(0, index + 1) + "["
									+ chordLine.substring(index).trim() + "]"
									+ line.substring(index + 1);
							chordLine = chordLine.substring(0, --index) + " ";
						}
						index--;
					}
					if (!chordLine.trim().equals("")) {
						line = "[" + chordLine.trim() + "]" + line.trim();
					}
					chordLine = null;
				}
				result += line + "\n";
			}
		}
		return result;
	}

	@Override
	public void start() {
		try {
			WebResponse artistListResponse = getArtistListPage();
			WebTable artistListTable = getArtistListTable(artistListResponse);
			List<WebLink> artistLinksList = getArtistLinksList(artistListTable);
			boolean process = false;
			for (WebLink link : artistLinksList) {
				if (process |= link.getURLString().equals(
						"cifras/idArtista/6084.htm")) {
					List<WebLink> musicList = getMusicLinkList(link);
					for (WebLink musicLink : musicList) {
						try {
							getAndSaveMusic(musicLink);
						} catch (Throwable e) {
							out.println("Error processing this URL: "
									+ musicLink.getURLString());
							e.printStackTrace();
						}
					}
				}
			}
		} catch (IOException e) {
			e.printStackTrace();
		} catch (SAXException e) {
			e.printStackTrace();
		}

	}

}
